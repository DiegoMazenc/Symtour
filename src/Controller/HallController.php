<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\Event;
use App\Entity\Hall;
use App\Entity\HallInfo;
use App\Entity\HallMember;
use App\Form\FilterSearchType;
use App\Form\HallType;
use App\Entity\Profil;
use App\Entity\RoleHall;
use App\Repository\BandRepository;
use App\Repository\HallRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/hall')]
class HallController extends AbstractController
{
    #[Route('/', name: 'app_hall_index', methods: ['GET', 'POST'])]
    public function index(Request $request, HallRepository $hallRepository): Response
    {
        $filter = $this->createForm(FilterSearchType::class);
        $filter->handleRequest($request);
        if ($filter->isSubmitted() && $filter->isValid()){
            $halls = $hallRepository->filter($filter->getData());
        }
        return $this->render('hall/index.html.twig', [
            'halls' => $hallRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hall_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TokenInterface $token): Response
    {
        $hall = new Hall();
        $form = $this->createForm(HallType::class, $hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hall);
            $entityManager->flush();
            $hallMember = new HallMember();
            $profil = $entityManager->getRepository(Profil::class)->findBy(["IdUser" => $token->getUser()]);
            $defaultRoleId = 1;
            $defaultRole = $entityManager->getRepository(RoleHall::class)->find($defaultRoleId);

            $hallMember->setHall($hall)
            ->setProfile($profil[0])
            ->setRole($defaultRole);
            $entityManager->persist($hallMember);
            $entityManager->flush();

            $hallInfo = new HallInfo;
            $hallInfo->setHall($hall);
            $entityManager->persist($hallInfo);
            $entityManager->flush();

            return $this->redirectToRoute('app_hall_info_edit', ["id"=>$hall->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hall/new.html.twig', [
            'hall' => $hall,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hall_show', methods: ['GET', 'POST'])]
    public function show(Hall $hall,BandRepository $bandRepository , Request $request, EntityManagerInterface $em, NotificationService $notification): Response
    {
        $notification->isRead((int)$request->query->get('notification_id'));
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $eventId = $request->request->get('event_id');
            $bandId = $request->request->get('bandId');
            $band = $bandRepository->find($bandId);
    
            if ($action === 'validate') {
                $status = 1; 
            } elseif ($action === 'reject') {
                $status = 2; 
            } else {
                $status = 3;
            }
            
            $event = $em->getRepository(Event::class)->find($eventId);
            if ($event) {
                $event->setStatus($status);
                $em->flush();
            }

            $hallName = $hall->getName();
            $hallId = $hall->getId();
            $receipt = "band";
            $sender = "hall";
            $type = "response";
            $notification->addNotificationBand($receipt,$hallName, $bandId, $sender, $hallId, $type, $band, $status, $em);
       
        }

        return $this->render('hall/show.html.twig', [
            'hall' => $hall,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hall_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hall $hall, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HallType::class, $hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hall_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hall/edit.html.twig', [
            'hall' => $hall,
            'form' => $form,
        ]);
    }

    

    #[Route('/{id}', name: 'app_hall_delete', methods: ['POST'])]
    public function delete(Request $request, Hall $hall, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hall->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hall);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hall_index', [], Response::HTTP_SEE_OTHER);
    }
}
