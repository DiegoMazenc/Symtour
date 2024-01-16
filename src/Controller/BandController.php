<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\BandInfo;
use App\Entity\BandMember;
use App\Entity\Profil;
use App\Entity\RoleBand;
use App\Form\BandType;
use App\Form\BandMemberType;
use App\Repository\BandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/band')]
class BandController extends AbstractController
{


    #[Route('/', name: 'app_band_index', methods: ['GET'])]
    public function index(BandRepository $bandRepository): Response
    {
        return $this->render('band/index.html.twig', [
            'bands' => $bandRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_band_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TokenInterface $token): Response
    {
        $band = new Band();
 
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            // dd($form);
            $entityManager->persist($band);
            $entityManager->flush();
            $bandMember = new BandMember();
            $profil = $entityManager->getRepository(Profil::class)->findBy(["IdUser" => $token->getUser()]);
            $defaultRoleId = 1;
            $defaultRole = $entityManager->getRepository(RoleBand::class)->find($defaultRoleId);
    
            $bandMember->setBand($band)
            ->setProfil($profil[0])
            ->setRole($defaultRole);
            $entityManager->persist($bandMember);
            $entityManager->flush();

            $bandInfo = new BandInfo();
            $bandInfo->setBandId($band); 
            $entityManager->persist($bandInfo);
            $entityManager->flush();



            return $this->redirectToRoute('app_band_show', ["id"=>$band->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('band/new.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_band_show', methods: ['GET'])]
    public function show(Band $band): Response
    {
        return $this->render('band/show.html.twig', [
            'band' => $band,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_band_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Band $band, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_band_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('band/edit.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit-member', name: 'app_band_edit_member', methods: ['GET', 'POST'])]
    public function editMember(Request $request, Band $band, EntityManagerInterface $entityManager, BandMember $bandMember): Response
    {
        $form = $this->createForm(BandMemberType::class, $bandMember);
        $form->handleRequest($request);

        return $this->render('band/edit_member.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_band_delete', methods: ['POST'])]
    public function delete(Request $request, Band $band, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$band->getId(), $request->request->get('_token'))) {
            $entityManager->remove($band);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_band_index', [], Response::HTTP_SEE_OTHER);
    }
}
