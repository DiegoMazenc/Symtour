<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\Hall;
use App\Entity\Event;
use App\Entity\Profil;
use App\Form\HallType;
use App\Entity\ChatRoom;
use App\Entity\HallInfo;
use App\Entity\RoleHall;
use App\Entity\BandEvent;
use App\Entity\HallMember;
use App\Form\SearchFormType;
use App\Form\AddRoleHallType;
use App\Form\FilterSearchType;
use App\Repository\BandRepository;
use App\Repository\HallRepository;
use App\Repository\EventRepository;
use App\Repository\ProfilRepository;
use App\Service\NotificationService;
use App\Repository\RoleHallRepository;
use App\Repository\BandEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/hall')]
class HallController extends AbstractController
{
    #[Route('/', name: 'app_hall_index', methods: ['GET', 'POST'])]
    public function index(Request $request, HallRepository $hallRepository): Response
    {
        $filter = $this->createForm(FilterSearchType::class);
        $filter->handleRequest($request);
        if ($filter->isSubmitted() && $filter->isValid()) {
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

            return $this->redirectToRoute('app_hall_info_edit', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hall/new.html.twig', [
            'hall' => $hall,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hall_show', methods: ['GET', 'POST'])]
    public function show(Hall $hall, BandRepository $bandRepository,EventRepository $eventRepository, Request $request, EntityManagerInterface $em, NotificationService $notification): Response
    {
        $eventCome = $eventRepository->getComeEventsByHallAsc($hall);
        $eventPast = $eventRepository->getPastEventsByHall($hall);

// dd($eventCome);
        $notification->isRead((int)$request->query->get('notification_id'));
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $eventId = $request->request->get('event_id');
            $bandId = $request->request->get('bandId');
            $band = $bandRepository->find($bandId);

            if ($action === 'validate') {
                $status = 1;

                // $chatRoom = new ChatRoom();

                // $chatRoom->setEvent($em->getRepository(Event::class)->find($eventId))
                // ->setDateCreate(new \DateTime());
                // $em->persist($chatRoom);
                // $em->flush();

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

            $notification->addNotificationBand("band", $hall->getName(), $bandId, "hall", $hall->getId(), "response", $band, $status, $em);
        }

        return $this->render('hall/show.html.twig', [
            'hall' => $hall,
            'eventCome' => $eventCome,
            'eventPast' => $eventPast
        ]);
    }

    #[Route('/{id}/members', name: 'app_hall_members', methods: ['GET', 'POST'])]

    public function bandMembers(
        NotificationService $notification,
        Hall $hall,
        RoleHallRepository $roleHallRepository,
        Request $request,
        ProfilRepository $profilRepository,
        EntityManagerInterface $em,
        HallRepository $hallRepository
    ): Response {
        $notification->isRead((int)$request->query->get('notification_id'));

        $roles = $roleHallRepository->findAll();

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $profil = '';
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchData = $searchForm->getData();
            $profil = $profilRepository->findBySearch($searchForm->isSubmitted() && $searchForm->isValid() ? $searchData['search'] : null);
        }

        $addRoleHall = $this->createForm(AddRoleHallType::class);
        $addRoleHall->handleRequest($request);
        $hallName = $hall->getName();

        if ($addRoleHall->isSubmitted() && $addRoleHall->isValid()) {
            $addRoleHallData = $addRoleHall->getData();
            $hallEntity = $hallRepository->find($addRoleHallData['hall']);
            $hallId = $hallEntity->getId();
            $roleEntity = $roleHallRepository->find($addRoleHallData['role']);
            $profilEntity = $profilRepository->find($addRoleHallData['profil']);
            $profilId = $profilEntity->getId();

            $hallMember = new HallMember();

            $hallMember
                ->setHall($hallEntity)
                ->setRole($roleEntity)
                ->setProfile($profilEntity);

            $em->persist($hallMember);
            $em->flush();


            $notification->addNotificationProfil("profil", $hallName, $profilId, "hall", $hallId, "add", $em);
        }

        return $this->render('hall/members.html.twig', [
            'hall' => $hall,
            'searchForm' => $searchForm->createView(),
            'addRoleHall' => $addRoleHall->createView(),
            'profil' => $profil,
            'roles' => $roles

        ]);
    }

    #[Route('/{id}/event', name: 'app_hall_event', methods: ['GET', 'POST'])]
    public function event(Hall $hall, Request $request,NotificationService $notification, BandRepository $bandRepository, EventRepository $eventRepository, EntityManagerInterface $em): Response
    {
        $eventCome = $eventRepository->getComeEventsByHallAsc($hall);
        $eventPast = $eventRepository->getPastEventsByHall($hall);
        $dateAdd = null;
        $bandFind = null;
        if ($request->isMethod('POST')) {
            $formName = $request->request->get('formName');

            if ($formName === 'addBandForm') {
                $addBand = $request->request->get('addBand');
                $dateAdd = $request->request->get('date');
                $bandFind = $bandRepository->findBySearch($addBand);
            } elseif ($formName === 'inviteBandForm') {

                $bandId = $request->request->get('bandId');
                $band = $bandRepository->find($bandId);
                $eventId = $request->request->get('eventId');
                $event = $eventRepository->find($eventId);

                $bandEvent = new BandEvent();

                $bandEvent
                    ->setEvent($event)
                    ->setBand($band)
                    ->setStatus("guest");

                $em->persist($bandEvent);
                $em->flush();

                $notification->addNotificationHallToBand("band", $hall->getName(), $band->getId(), "hall", $hall->getId(), "guest", $band, $em);
                
        return $this->redirectToRoute('app_hall_event', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);

            
        }}

        return $this->render('hall/event.html.twig', [
            'hall' => $hall,
            'eventCome' => $eventCome,
            'eventPast' => $eventPast,
            'band' => $bandFind,
            'dateAdd' => $dateAdd
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

    #[Route('/{id}/event-delete', name: 'app_hall_event_delete', methods: ['POST'])]
    public function deleteEvent(Request $request,Hall $hall, BandEventRepository $bandEventRepository, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $eventId = $request->request->get('idEvent');
            
            $bandEvent = $bandEventRepository->find($eventId);
    
            if ($bandEvent) {
                $entityManager->remove($bandEvent);
                $entityManager->flush();
            }
    
        }
        

        return $this->redirectToRoute('app_hall_event', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_hall_delete', methods: ['POST'])]
    public function delete(Request $request, Hall $hall, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $hall->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hall);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hall_index', [], Response::HTTP_SEE_OTHER);
    }
}
