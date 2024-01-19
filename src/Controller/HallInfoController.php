<?php

namespace App\Controller;

use App\Entity\HallInfo;
use App\Form\HallInfoType;
use App\Repository\HallInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hall/info')]
class HallInfoController extends AbstractController
{
    #[Route('/', name: 'app_hall_info_index', methods: ['GET'])]
    public function index(HallInfoRepository $hallInfoRepository): Response
    {
        return $this->render('hall_info/index.html.twig', [
            'hall_infos' => $hallInfoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hall_info_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hallInfo = new HallInfo();
        $form = $this->createForm(HallInfoType::class, $hallInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hallInfo);
            $entityManager->flush();

            return $this->redirectToRoute('app_hall_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hall_info/new.html.twig', [
            'hall_info' => $hallInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hall_info_show', methods: ['GET'])]
    public function show(HallInfo $hallInfo): Response
    {
        return $this->render('hall_info/show.html.twig', [
            'hall_info' => $hallInfo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hall_info_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HallInfo $hallInfo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HallInfoType::class, $hallInfo);
        $form->handleRequest($request);
        // dd($hallInfo);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hall_info_edit', ["id"=>$hallInfo->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->render('hall_info/edit.html.twig', [
            'hall_info' => $hallInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hall_info_delete', methods: ['POST'])]
    public function delete(Request $request, HallInfo $hallInfo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hallInfo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hallInfo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hall_info_index', [], Response::HTTP_SEE_OTHER);
    }
}
