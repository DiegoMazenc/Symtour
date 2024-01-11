<?php

namespace App\Controller;

use App\Entity\RoleHall;
use App\Form\RoleHallType;
use App\Repository\RoleHallRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/role/hall')]
class RoleHallController extends AbstractController
{
    #[Route('/', name: 'app_role_hall_index', methods: ['GET'])]
    public function index(RoleHallRepository $roleHallRepository): Response
    {
        return $this->render('role_hall/index.html.twig', [
            'role_halls' => $roleHallRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_role_hall_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $roleHall = new RoleHall();
        $form = $this->createForm(RoleHallType::class, $roleHall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($roleHall);
            $entityManager->flush();

            return $this->redirectToRoute('app_role_hall_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('role_hall/new.html.twig', [
            'role_hall' => $roleHall,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_role_hall_show', methods: ['GET'])]
    public function show(RoleHall $roleHall): Response
    {
        return $this->render('role_hall/show.html.twig', [
            'role_hall' => $roleHall,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_role_hall_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RoleHall $roleHall, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoleHallType::class, $roleHall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_role_hall_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('role_hall/edit.html.twig', [
            'role_hall' => $roleHall,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_role_hall_delete', methods: ['POST'])]
    public function delete(Request $request, RoleHall $roleHall, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$roleHall->getId(), $request->request->get('_token'))) {
            $entityManager->remove($roleHall);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_role_hall_index', [], Response::HTTP_SEE_OTHER);
    }
}
