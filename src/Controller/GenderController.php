<?php

namespace App\Controller;

use App\Entity\Gender;
use App\Form\GenderType;
use App\Repository\GenderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gender')]
class GenderController extends AbstractController
{
    #[Route('/', name: 'app_gender_index', methods: ['GET'])]
    public function index(GenderRepository $genderRepository): Response
    {
        return $this->render('gender/index.html.twig', [
            'genders' => $genderRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gender_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gender = new Gender();
        $form = $this->createForm(GenderType::class, $gender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gender);
            $entityManager->flush();

            return $this->redirectToRoute('app_gender_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gender/new.html.twig', [
            'gender' => $gender,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gender_show', methods: ['GET'])]
    public function show(Gender $gender): Response
    {
        return $this->render('gender/show.html.twig', [
            'gender' => $gender,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gender_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gender $gender, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GenderType::class, $gender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gender_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gender/edit.html.twig', [
            'gender' => $gender,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gender_delete', methods: ['POST'])]
    public function delete(Request $request, Gender $gender, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gender->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gender);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gender_index', [], Response::HTTP_SEE_OTHER);
    }
}
