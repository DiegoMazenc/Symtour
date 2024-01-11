<?php

namespace App\Controller;

use App\Entity\MusicCategory;
use App\Form\MusicCategoryType;
use App\Repository\MusicCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/music/category')]
class MusicCategoryController extends AbstractController
{
    #[Route('/', name: 'app_music_category_index', methods: ['GET'])]
    public function index(MusicCategoryRepository $musicCategoryRepository): Response
    {
        return $this->render('music_category/index.html.twig', [
            'music_categories' => $musicCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_music_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $musicCategory = new MusicCategory();
        $form = $this->createForm(MusicCategoryType::class, $musicCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($musicCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_music_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('music_category/new.html.twig', [
            'music_category' => $musicCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_music_category_show', methods: ['GET'])]
    public function show(MusicCategory $musicCategory): Response
    {
        return $this->render('music_category/show.html.twig', [
            'music_category' => $musicCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_music_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MusicCategory $musicCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MusicCategoryType::class, $musicCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_music_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('music_category/edit.html.twig', [
            'music_category' => $musicCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_music_category_delete', methods: ['POST'])]
    public function delete(Request $request, MusicCategory $musicCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$musicCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($musicCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_music_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
