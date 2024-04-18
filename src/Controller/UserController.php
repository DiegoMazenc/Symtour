<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profil;
use App\Form\UserType;
use App\Form\UserMailType;
use App\Form\EditPasswordType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository,TokenInterface $token, MailerInterface $mailer): Response
    {
        $user = $token->getUser();

        return $this->redirectToRoute('app_profil_show', ["id" => $user->getProfil()->getId()]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,TokenInterface $token, UserPasswordHasherInterface $hasher): Response
    {
        $user = $token->getUser();

        return $this->redirectToRoute('app_profil_show', ["id" => $user->getProfil()->getId()]);

    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->redirectToRoute('app_profil_show', ["id" => $user->getProfil()->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordUser, TokenInterface $token,  Security $security): Response
    {
        if ((!$this->isGranted('user_edit', $user) && $security->getUser() !== $user)) {
            $user = $token->getUser();
            return $this->redirectToRoute('app_user_edit', ["id" => $user->getProfil()->getId()], Response::HTTP_SEE_OTHER);
        }
        $formPassword = $this->createForm(EditPasswordType::class);
        $formPassword->handleRequest($request);

        $denied = false;
        $error = false;

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $mdpActuel = $formPassword->get('mdpActuel')->getData();
            $verifNewPass = $formPassword->get('verifNewPass')->getData();
            $confirmNewPass = $formPassword->get('confirmNewPass')->getData();
            if ($passwordUser->isPasswordValid($user, $mdpActuel)) {
                if ($verifNewPass == $confirmNewPass) {
                    $user->setPassword($passwordUser->hashPassword($user, $confirmNewPass));
                    $entityManager->flush();
                    $this->addFlash(
                        'success',
                        'Votre mot de passe à été mis à jour'
                    );
                } else {
                    $error = true;
                }
            } else {
                $denied = true;
            }
        }

        $formMail = $this->createForm(UserMailType::class, $user);
        $formMail->handleRequest($request);

        if ($formMail->isSubmitted() && $formMail->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre Email à été mis à jour'
            );
        }

        return $this->render('user/edit.html.twig', [
            'error' => $error,
            'denied' => $denied,
            'user' => $user,
            'formPassword' => $formPassword,
            'formMail' => $formMail,

        ]);
    }
    // #[Route('/{id}', name: 'app_user_delete', methods: ['POST']), isGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, TokenInterface $token,  Security $security): Response
    {
        if ((!$this->isGranted('user_edit', $user) && $security->getUser() !== $user) || $this->isGranted('ROLE_ADMIN')) {
            $user = $token->getUser();
            return $this->redirectToRoute('app_user_edit', ["id" => $user->getProfil()->getId()], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $profil = $user->getProfil();
    
            // Supprimer les enregistrements liés à band_member_role
            foreach ($profil->getBandMembers() as $member) {
                foreach ($member->getBandMemberRoles() as $role) {
                    $entityManager->remove($role);
                }
                $entityManager->remove($member);
            }

            foreach ($profil->getNotifications() as $notif)
            {
                $entityManager->remove($notif);

            }
    
            // Supprimer les enregistrements liés à hall_member_role
            foreach ($profil->getHallMembers() as $member) {
                foreach ($member->getHallMemberRoles() as $role) {
                    $entityManager->remove($role);
                }
                $entityManager->remove($member);
            }
    
            // Supprimer le profil
            $entityManager->remove($profil);
    
            // Supprimer l'utilisateur
            $entityManager->remove($user);
    
            $entityManager->flush();
            $this->addFlash(
               'success',
               'Votre profil à bien été supprimé, Bonne continuation !'
            );
        }
    
        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }
}
