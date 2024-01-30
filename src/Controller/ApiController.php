<?php

namespace App\Controller;

use App\Repository\HallRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api', methods: ['GET'])]
    public function index(HallRepository $hallRepository): Response
    {
        $hall = $hallRepository->findAll();
        return $this->json($hall);
    }

    #[Route('/api', name: 'app_api', methods: ['POST'])]
    public function hall(Request $request, HallRepository $hallRepository): Response
    {
        $hall = [];
        dd($request->request);
        return $this->json($hall);
    }
}
