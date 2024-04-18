<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\User;
use App\Form\BandInfoType;
use App\Form\BandType;
use App\Form\UserType;
use App\Form\ProfilType;
use App\Repository\BandRepository;

use App\Repository\HallRepository;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/hall', name: 'app_admin_hall', methods: ['GET', 'POST'])]
    public function hall(HallRepository $hallRepository): Response
    {
        $halls = $hallRepository->findAll();
        return $this->render('admin/hall.html.twig', [
            'halls' => $halls
        ]);
    }


    #[Route('/hall/{id}/edit', name: 'app_admin_hall_edit', methods: ['GET', 'POST'])]
    public function hallEdit(HallRepository $hallRepository): Response
    {
        $halls = $hallRepository->findAll();
        return $this->render('admin/hall_edit.html.twig', [
            'halls' => $halls
        ]);
    }
}