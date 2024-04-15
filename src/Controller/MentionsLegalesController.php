<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class MentionsLegalesController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_mentionslegales', methods: ['GET'])]
    public function mentionsLegales(): Response
    {
        return $this->render('mentionslegales.html.twig', [
        ]);
    }

    #[Route('/cgu', name: 'app_cgu', methods: ['GET'])]
    public function cgu(): Response
    {
        return $this->render('cgu.html.twig', [
        ]);
    }
}