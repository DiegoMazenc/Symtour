<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profil;
use App\Form\ProfilType;
use Doctrine\ORM\Mapping\Id;
use App\Repository\ProfilRepository;
use App\Repository\BandMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profil')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil_index', methods: ['GET'])]
    public function index(ProfilRepository $profilRepository): Response
    {
        return $this->render('profil/index.html.twig', [
            'profils' => $profilRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_profil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $profil = new Profil();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/new.html.twig', [
            'profil' => $profil,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profil_show', methods: ['GET'])]
    public function show(Profil $profil, ProfilRepository $profilRepository, BandMemberRepository $bandMemberRepository, NotificationRepository $notificationRepository): Response
    {
        $count = $notificationRepository->count(['status' => 1, 'profil' => $profil->getId()]);
;
        return $this->render('profil/show.html.twig', [
            'profil' => $profil,
            'notificationCount' => $count,


        ]);
    }

    #[Route('/{id}/notification', name: 'app_profil_notification', methods: ['GET'])]
    public function notification(Profil $profil, ProfilRepository $profilRepository, BandMemberRepository $bandMemberRepository): Response
    {
        
        return $this->render('profil/notification.html.twig', [
            'profil' => $profil,

        ]);
    }

    

    #[Route('/{id}/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profil $profil, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();

            return $this->redirectToRoute('app_profil_show', ["id"=>$profil->getId()], Response::HTTP_SEE_OTHER);
        }
       
        return $this->render('profil/edit.html.twig', [
            'profil' => $profil,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(Request $request, Profil $profil, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profil->getId(), $request->request->get('_token'))) {
            $entityManager->remove($profil);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
    }
}
