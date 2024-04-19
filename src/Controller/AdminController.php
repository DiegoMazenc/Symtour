<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\Hall;
use App\Entity\User;
use App\Form\BandType;
use App\Form\HallType;
use App\Form\UserType;
use App\Form\ProfilType;
use App\Form\BandInfoType;

use App\Form\HallInfoType;
use App\Repository\BandRepository;
use App\Repository\EventRepository;
use App\Repository\HallRepository;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use App\Repository\BandInfoRepository;
use App\Repository\HallInfoRepository;
use App\Repository\BandEventRepository;
use App\Repository\BandMemberRepository;
use App\Repository\HallMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\BandEvent;
use App\Repository\BandMemberRoleRepository;
use App\Repository\HallMemberRoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_index', methods: ['GET'])]
    public function index(): Response
    {

        return $this->render('admin/index.html.twig', [
            'coucou' => 'coucou'
        ]);
    }

    #[Route('/user', name: 'app_admin_user', methods: ['GET', 'POST'])]
    public function user(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/user.html.twig', [
            'users' => $users
        ]);
    }
    #[Route('/user/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function userEdit(Request $request, UserRepository $userRepository, User $user, FormFactoryInterface $formFactory, EntityManagerInterface $em): Response
    {
        $formUser = $formFactory->create(UserType::class, $user);
        $formUser->remove('password');

        $formUser->add('roles', ChoiceType::class, [
            'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN'
            ],
            'label' => 'Rôle',
            'expanded' => false,
            'multiple' => true,
            'choice_label' => function ($choice, $key, $value) {
                // Utiliser la constante ROLE_USER ou ROLE_ADMIN comme label
                if ($value === 'ROLE_USER') {
                    return 'Utilisateur';
                } elseif ($value === 'ROLE_ADMIN') {
                    return 'Administrateur';
                }
                return $value;
            },
        ]);

        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $data = $formUser->getData();
            $role = [];
            $roleCount = 0;
            foreach ($data->getRoles() as $role) {
                if ($role == "ROLE_USER") {
                    $roleCount++;
                }
            }
            $roleCount == 2 ? $role = ["ROLE_USER"] : $role = ["ROLE_ADMIN"];
            $user->setRoles($role);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
        }
        $profil = $user->getProfil();
        $formProfil = $formFactory->create(ProfilType::class, $profil);
        $formProfil->remove('password');

        $formProfil->handleRequest($request);

        // Vérifier si le formulaire est valide
        if ($formProfil->isSubmitted() && $formProfil->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
        }

        return $this->render('admin/user_edit.html.twig', [
            'user' => $user,
            'formUser' => $formUser->createView(),
            'formProfil' => $formProfil->createView(),
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['GET', 'POST'])]
    public function userDelete(Request $request, User $user, EntityManagerInterface $entityManager, Security $security): Response
    {

        $user->getProfil() ? $profil = $user->getProfil() : $profil = null;

        if ($profil) {

            foreach ($profil->getBandMembers() as $member) {
                foreach ($member->getBandMemberRoles() as $role) {
                    $entityManager->remove($role);
                }
                $entityManager->remove($member);
            }

            foreach ($profil->getNotifications() as $notif) {
                $entityManager->remove($notif);
            }

            foreach ($profil->getHallMembers() as $member) {
                foreach ($member->getHallMemberRoles() as $role) {
                    $entityManager->remove($role);
                }
                $entityManager->remove($member);
            }

            $entityManager->remove($profil);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/band', name: 'app_admin_band', methods: ['GET', 'POST'])]
    public function band(BandRepository $bandRepository): Response
    {
        $bands = $bandRepository->findAll();
        return $this->render('admin/band.html.twig', [
            'bands' => $bands
        ]);
    }

    #[Route('/band/{id}/edit', name: 'app_admin_band_edit', methods: ['GET', 'POST'])]
    public function bandEdit(BandRepository $bandRepository, Band $band, Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $em): Response
    {
        $formBand = $formFactory->create(BandType::class, $band);
        $formBand->handleRequest($request);

        if ($formBand->isSubmitted() && $formBand->isValid()) {

            $em->persist($band);
            $em->flush();

            return $this->redirectToRoute('app_admin_band_edit', ['id' => $band->getId()]);
        }

        $formBandInfo = $formFactory->create(BandInfoType::class, $band->getBandInfo());
        $formBandInfo->handleRequest($request);

        if ($formBandInfo->isSubmitted() && $formBandInfo->isValid()) {

            $em->persist($band);
            $em->flush();

            return $this->redirectToRoute('app_admin_band_edit', ['id' => $band->getId()]);
        }

        return $this->render('admin/band_edit.html.twig', [
            'band' => $band,
            'formBand' => $formBand,
            'formBandInfo' => $formBandInfo

        ]);
    }
    #[Route('/band/{id}/delete', name: 'app_admin_band_delete', methods: ['GET', 'POST'])]
    public function bandDelete(
        Security $security,
        Request $request,
        BandMemberRepository $bandMemberRepository,
        BandMemberRoleRepository $bandMemberRoleRepository,
        BandInfoRepository $bandInfoRepository,
        BandEventRepository $bandEventRepository,
        Band $band,
        EntityManagerInterface $entityManager
    ): Response {

        $allBandMembers = $bandMemberRepository->findBy(['band' => $band]);
        $bandEvents = $bandEventRepository->findBy(['band' => $band->getId()]);
        $bandInfo = $bandInfoRepository->findOneBy(['bandId' => $band->getId()]);

        foreach ($allBandMembers as $bandMember) {
            $roleMember = $bandMemberRoleRepository->findOneBy(['band_member' => $bandMember->getId()]);
            $entityManager->remove($roleMember);
            $entityManager->remove($bandMember);
        }
        foreach ($bandEvents as $event) {
            $entityManager->remove($event);
        }
        $entityManager->remove($bandInfo);
        $entityManager->remove($band);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_band', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/hall', name: 'app_admin_hall', methods: ['GET', 'POST'])]
    public function hall(HallRepository $hallRepository): Response
    {
        $halls = $hallRepository->findAll();
        return $this->render('admin/hall.html.twig', [
            'halls' => $halls
        ]);
    }


    #[Route('/hall/{id}/edit', name: 'app_admin_hall_edit', methods: ['GET', 'POST'])]
    public function hallEdit(HallRepository $hallRepository, Hall $hall, Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $em): Response
    {
        $formHall = $formFactory->create(HallType::class, $hall);
        $formHall->handleRequest($request);

        if ($formHall->isSubmitted() && $formHall->isValid()) {

            $em->persist($hall);
            $em->flush();

            return $this->redirectToRoute('app_admin_hall_edit', ['id' => $hall->getId()]);
        }

        $formHallInfo = $formFactory->create(HallInfoType::class, $hall->getHallInfo());
        $formHallInfo->handleRequest($request);

        if ($formHallInfo->isSubmitted() && $formHallInfo->isValid()) {

            $em->persist($hall);
            $em->flush();

            return $this->redirectToRoute('app_admin_hall_edit', ['id' => $hall->getId()]);
        }

        return $this->render('admin/hall_edit.html.twig', [
            'hall' => $hall,
            'formHall' => $formHall,
            'formHallInfo' => $formHallInfo

        ]);
    }

    #[Route('/hall/{id}/delete', name: 'app_admin_hall_delete', methods: ['GET', 'POST'])]
    public function hallDelete(
        Security $security,
        Request $request,
        HallMemberRepository $hallMemberRepository,
        HallMemberRoleRepository $hallMemberRoleRepository,
        HallInfoRepository $hallInfoRepository,
        EventRepository $eventRepository,
        BandEventRepository $bandEventRepository,
        Hall $hall,
        EntityManagerInterface $entityManager
    ): Response {

        $allHallMembers = $hallMemberRepository->findBy(['hall' => $hall]);
        $hallInfo = $hallInfoRepository->find($hall);
        $events = $eventRepository->findBy(['hall' => $hall]);

        foreach ($events as $event) {
            $bandEvents = $bandEventRepository->findBy(['event' => $event]);
            foreach ($bandEvents as $bandEvent) {
                $entityManager->remove($bandEvent);
            }
            $entityManager->remove($event);
        }

        foreach ($allHallMembers as $hallmember) {
            $roleMember = $hallMemberRoleRepository->findOneBy(['hall_member' => $hallmember->getId()]);
            $entityManager->remove($roleMember);
            $entityManager->remove($hallmember);
        }
        $entityManager->remove($hallInfo);
        $entityManager->remove($hall);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_hall', [], Response::HTTP_SEE_OTHER);

    }
}