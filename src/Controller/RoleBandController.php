<?php

namespace App\Controller;

use App\Entity\RoleBand;
use App\Form\RoleBandType;
use App\Repository\RoleBandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/role/band')]
class RoleBandController extends AbstractController
{
    #[Route('/', name: 'app_role_band_index', methods: ['GET'])]
    public function index(RoleBandRepository $roleBandRepository): Response
    {
        return $this->render('role_band/index.html.twig', [
            'role_bands' => $roleBandRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_role_band_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $roleBand = new RoleBand();
        $form = $this->createForm(RoleBandType::class, $roleBand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($roleBand);
            $entityManager->flush();

            return $this->redirectToRoute('app_role_band_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('role_band/new.html.twig', [
            'role_band' => $roleBand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_role_band_show', methods: ['GET'])]
    public function show(RoleBand $roleBand): Response
    {
        return $this->render('role_band/show.html.twig', [
            'role_band' => $roleBand,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_role_band_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RoleBand $roleBand, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoleBandType::class, $roleBand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_role_band_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('role_band/edit.html.twig', [
            'role_band' => $roleBand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_role_band_delete', methods: ['POST'])]
    public function delete(Request $request, RoleBand $roleBand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$roleBand->getId(), $request->request->get('_token'))) {
            $entityManager->remove($roleBand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_role_band_index', [], Response::HTTP_SEE_OTHER);
    }
}
