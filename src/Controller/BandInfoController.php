<?php

namespace App\Controller;

use App\Entity\BandInfo;
use App\Form\BandInfoType;
use App\Repository\BandInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/band/info')]
class BandInfoController extends AbstractController
{
    #[Route('/', name: 'app_band_info_index', methods: ['GET'])]
    public function index(BandInfoRepository $bandInfoRepository): Response
    {
        return $this->render('band_info/index.html.twig', [
            'band_infos' => $bandInfoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_band_info_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bandInfo = new BandInfo();
        $form = $this->createForm(BandInfoType::class, $bandInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bandInfo);
            $entityManager->flush();

            return $this->redirectToRoute('app_band_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('band_info/new.html.twig', [
            'band_info' => $bandInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_band_info_show', methods: ['GET'])]
    public function show(BandInfo $bandInfo): Response
    {
        return $this->render('band_info/show.html.twig', [
            'band_info' => $bandInfo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_band_info_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BandInfo $bandInfo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BandInfoType::class, $bandInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_band_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('band_info/edit.html.twig', [
            'band_info' => $bandInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_band_info_delete', methods: ['POST'])]
    public function delete(Request $request, BandInfo $bandInfo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bandInfo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($bandInfo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_band_info_index', [], Response::HTTP_SEE_OTHER);
    }
}
