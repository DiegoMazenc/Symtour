<?php

namespace App\Controller;

use App\Form\FilterSearchType;
use App\Repository\MusicCategoryRepository;
use App\Repository\HallRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET', 'POST'])]
    public function index(Request $request, HallRepository $hallRepository, MusicCategoryRepository $musicCategoryRepository): Response
    {
        $filter = $this->createForm(FilterSearchType::class);
        $filter->handleRequest($request);
        $halls = $hallRepository->findAll();
        if ($filter->isSubmitted() && $filter->isValid()){
            $halls = $hallRepository->filter($filter->getData());
        }
        // dd($filter->getData());
        // dd($filter->createView()->children);
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'halls' => $halls,
            'music_categories' => $musicCategoryRepository->findAll(),
            'filterForm' => $filter->createView()
        ]);
    }
}
