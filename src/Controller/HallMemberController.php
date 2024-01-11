<?php

namespace App\Controller;

use App\Entity\HallMember;
use App\Form\HallMemberType;
use App\Repository\HallMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hall/member')]
class HallMemberController extends AbstractController
{
    #[Route('/', name: 'app_hall_member_index', methods: ['GET'])]
    public function index(HallMemberRepository $hallMemberRepository): Response
    {
        return $this->render('hall_member/index.html.twig', [
            'hall_members' => $hallMemberRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hall_member_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hallMember = new HallMember();
        $form = $this->createForm(HallMemberType::class, $hallMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hallMember);
            $entityManager->flush();

            return $this->redirectToRoute('app_hall_member_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hall_member/new.html.twig', [
            'hall_member' => $hallMember,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hall_member_show', methods: ['GET'])]
    public function show(HallMember $hallMember): Response
    {
        return $this->render('hall_member/show.html.twig', [
            'hall_member' => $hallMember,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hall_member_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HallMember $hallMember, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HallMemberType::class, $hallMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hall_member_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hall_member/edit.html.twig', [
            'hall_member' => $hallMember,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hall_member_delete', methods: ['POST'])]
    public function delete(Request $request, HallMember $hallMember, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hallMember->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hallMember);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hall_member_index', [], Response::HTTP_SEE_OTHER);
    }
}
