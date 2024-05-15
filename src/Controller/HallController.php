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
use App\Entity\BandMember;
use App\Entity\HallMember;
use App\Form\HallInfoType;
use App\Form\SearchFormType;
use App\Form\AddRoleHallType;
use App\Entity\HallMemberRole;
use App\Form\FilterSearchType;
use App\Service\AddPhotosService;
use App\Repository\BandRepository;
use App\Repository\HallRepository;
use App\Repository\EventRepository;
use App\Repository\ProfilRepository;
use App\Service\NotificationService;
use App\Repository\HallInfoRepository;
use App\Repository\RoleHallRepository;
use App\Repository\BandEventRepository;
use App\Repository\HallMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HallMemberRoleRepository;
use PHPUnit\Framework\Constraint\IsEmpty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
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
    public function new(Request $request, EntityManagerInterface $entityManager, TokenInterface $token, AddPhotosService $addPhotosService): Response
    {
        $hall = new Hall();
        $form = $this->createForm(HallType::class, $hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('music_category')->getData()->IsEmpty()) {
                // dd('coucou');
                $this->addFlash(
                    'errorMusicCategory',
                    'Veuillez sélectionner au moins une catégorie musicale accepté'
                );
            } else {

                $img = $form->get('logo')->getData();
                if ($img) {
                    $addPhotosService->addNewPicture($img, $hall);
                } else {
                    $hall->setLogo('/assets/img/profil_hall.png');
                }

                $entityManager->persist($hall);
                $entityManager->flush();
                $hallMember = new HallMember();
                $profil = $entityManager->getRepository(Profil::class)->findBy(["IdUser" => $token->getUser()]);
                $defaultRoleId = 1;
                $defaultRole = $entityManager->getRepository(RoleHall::class)->find($defaultRoleId);

                $hallMember->setHall($hall)
                    ->setProfile($profil[0])
                    ->setStatus("admin");
                $entityManager->persist($hallMember);
                $entityManager->flush();

                $hallInfo = new HallInfo;
                $hallInfo->setHall($hall);
                $entityManager->persist($hallInfo);
                $entityManager->flush();

                $hallMemberRole = new HallMemberRole();
                $hallMemberRole
                    ->setHallMember($hallMember)
                    ->setRoleHall($defaultRole);
                $entityManager->persist($hallMemberRole);
                $entityManager->flush();

                return $this->redirectToRoute('app_hall_infos', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->render('hall/new.html.twig', [
            'hall' => $hall,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hall_show', methods: ['GET', 'POST'])]
    public function show(Hall $hall, BandRepository $bandRepository, HallMemberRepository $hallMemberRepository, BandEventRepository $bandEventRepository, EventRepository $eventRepository, Request $request, EntityManagerInterface $em, NotificationService $notification): Response
    {

        $allHallMembers = $hallMemberRepository->findBy(['hall' => $hall]);
        $canAccess = false;

        foreach ($allHallMembers as $hallMember) {
            if ($this->isGranted('hall_member', $hallMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_search_booking', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        }

        $eventCome = $eventRepository->getComeEventsByHallAsc($hall);
        $eventPast = $eventRepository->getPastEventsByHallForShow($hall);
        $eventAll = $eventRepository->getAllEventsByHall($hall);

        $notification->isRead((int) $request->query->get('notification_id'));
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $eventId = $request->request->get('eventid');
            $bandId = $request->request->get('bandId');
            $eventDate = $request->request->get('eventDate');
            $band = $bandRepository->find($bandId);
            $otherDates = null;
            $otherBandDates = null;

            if ($action === 'validate') {
                $status = 1;
                $otherDates = $eventRepository->getOtherDateForReject($eventDate, $hall, $eventId);
                $otherBandDates = $bandEventRepository->getOtherDateForReject($eventDate, $band, $eventId);

            } elseif ($action === 'reject') {
                $status = 2;
            } else {
                $status = 3;
            }

            $event = $em->getRepository(Event::class)->find($eventId);
            if ($event) {
                $event->setStatus($status);
                if ($otherDates) {
                    foreach ($otherDates as $d) {
                        $d->setStatus(2);
                        foreach ($d->getBandEvents() as $bandEvent) {
                            $bandReject = $bandEvent->getBand();
                            $notification->addNotificationBand("band", $hall->getName(), $bandReject->getId(), "hall", $hall->getId(), "response", $bandReject, 2, $em);
                        }
                    }
                }
                if ($otherBandDates) {
                    foreach ($otherBandDates as $bd) {
                        $eventBD = $bd->getEvent();
                        $notification->addNotificationHall("hall", $band->getName(), $eventBD->getHall()->getId(), "band", $band->getId(), "cancel", $eventBD->getHall(), $em);
                        $em->remove($eventBD);
                        $em->remove($bd);

                    }
                }
                $em->flush();
            }

            $notification->addNotificationBand("band", $hall->getName(), $bandId, "hall", $hall->getId(), "response", $band, $status, $em);
        }


        return $this->render('hall/show.html.twig', [
            'hall' => $hall,
            'eventCome' => $eventCome,
            'eventPast' => $eventPast,
            'eventAll' => $eventAll,
        ]);
    }

    #[Route('/{id}/members', name: 'app_hall_members', methods: ['GET', 'POST'])]

    public function bandMembers(
        TokenInterface $token,
        NotificationService $notification,
        Hall $hall,
        RoleHallRepository $roleHallRepository,
        Request $request,
        ProfilRepository $profilRepository,
        EntityManagerInterface $em,
        HallMemberRepository $hallMemberRepository,
        HallMemberRoleRepository $hallMemberRoleRepository,
        HallRepository $hallRepository
    ): Response {
        $notification->isRead((int) $request->query->get('notification_id'));

        $allHallMembers = $hallMemberRepository->findBy(['hall' => $hall]);
        $canAccess = false;

        foreach ($allHallMembers as $hallMember) {
            if ($this->isGranted('hall_member', $hallMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_search_booking', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        }

        $roles = $roleHallRepository->findAll();

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $profil = '';
        $hallName = $hall->getName();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchData = $searchForm->getData();
            $profil = $profilRepository->findBySearch($searchForm->isSubmitted() && $searchForm->isValid() ? $searchData['search'] : null);
        }

        if ($request->isMethod('POST') && $request->request->has('roleId') && $request->request->has('profilId') && $request->request->has('hallId')) {
            $profilId = $request->request->get('profilId');
            $profil = $profilRepository->find($profilId);
            $hallId = $request->request->get('hallId');
            $hall = $hallRepository->find($hallId);
            $roleId = $request->request->get('roleId');
            $role = $roleHallRepository->find($roleId);

            $hallMember = new HallMember();
            $hallMember
                ->setHall($hall)
                ->setProfile($profil)
                ->setStatus('guest');

            $em->persist($hallMember);

            $hallMemberRole = new HallMemberRole();
            $hallMemberRole->setHallMember($hallMember)
                ->setRoleHall($role);
            $em->persist($hallMemberRole);
            $em->flush();

            $notification->addNotificationProfil("profil", $hallName, $profilId, "hall", $hallId, "add", $em);
        }


        if ($request->isMethod('POST')) {
            $member = $request->request->get('memberId');

            foreach ($request->request->all() as $key => $value) {
                if (strpos($key, 'role_') !== false) {
                    $idRole = $value;
                    $role = $roleHallRepository->find((int) $idRole);
                    $idMemberRoleHall = $request->request->get("idMemberRoleHall_" . substr($key, 5));
                    $MemberRoleHall = $hallMemberRoleRepository->find((int) $idMemberRoleHall);

                    if ($MemberRoleHall) {
                        $MemberRoleHall->setRoleHall($role);
                    }

                }

                if ($request->request->has("deleteRole_" . substr($key, 5))) {
                    $idMemberRoleHallToDelete = $request->request->get("idMemberRoleHall_" . substr($key, 5));
                    $memberRoleToDelete = $hallMemberRoleRepository->find((int) $idMemberRoleHallToDelete);
                    if ($memberRoleToDelete) {
                        $em->remove($memberRoleToDelete);
                        $em->flush();
                    } else {

                    }
                }
            }

            $status = $request->request->get('status');
            $hallMember = $hallMemberRepository->find((int) $member);

            if ($hallMember) {
                $hall = $hallMember->getHall();
                $countAdmin = $hallMemberRepository->countAdmin($hall, 'admin');

                if ($status != "admin" && $countAdmin < 2) {
                    $this->addFlash('denied', 'Vous devez élire un nouvel Admin !');
                } else {
                    $hallMember->setStatus($status);
                    $em->persist($hallMember);
                    $em->flush();
                }
            }

            $newRole = $request->request->get('newRole');
            $role = $roleHallRepository->find((int) $newRole);

            if ($newRole != "1") {
                $newMemberRoleHall = new HallMemberRole;

                $newMemberRoleHall
                    ->setHallMember($hallMember)
                    ->setRoleHall($role);

                $em->persist($newMemberRoleHall);
                $em->flush();
            }

            if ($request->request->has("deleteMember")) {
                $idMember = $request->request->get('idMember');
                $member = $hallMemberRepository->find($idMember);

                if ($member) {
                    $hallMemberRoles = $hallMemberRoleRepository->findBy(['hall_member' => $member]);

                    foreach ($hallMemberRoles as $hallMemberRole) {
                        $em->remove($hallMemberRole);
                    }

                    $em->flush();

                    $em->remove($member);
                    $em->flush();

                }
            }
        }

        return $this->render('hall/members.html.twig', [
            'hall' => $hall,
            'searchForm' => $searchForm->createView(),
            // 'addRoleHall' => $addRoleHall->createView(),
            'profil' => $profil,
            'roles' => $roles

        ]);
    }

    #[Route('/{id}/event', name: 'app_hall_event', methods: ['GET', 'POST'])]
    public function event(
        Hall $hall,
        HallRepository $hallRepository,
        HallMemberRepository $hallMemberRepository,
        Request $request,
        NotificationService $notification,
        BandRepository $bandRepository,
        EventRepository $eventRepository,
        EntityManagerInterface $em
    ): Response {

        $allHallMembers = $hallMemberRepository->findBy(['hall' => $hall]);
        $canAccess = false;

        foreach ($allHallMembers as $hallMember) {
            if ($this->isGranted('hall_member', $hallMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_search_booking', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        }

        $eventCome = $eventRepository->getComeEventsByHallAsc($hall);
        $eventPast = $eventRepository->getPastEventsByHall($hall);
        $dateAdd = null;
        $bandFind = null;
        $filterEvent = "all";

        if ($request->isMethod('POST') && $request->request->get('formName')) {
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
            }
        }


        if ($request->isMethod('POST') && $request->request->get('filter')) {
            $filter = $request->request->get('filter');

            if ($filter == 'all') {
                $filterEvent = "all";
            } elseif ($filter == 'valid') {
                $filterEvent = "valid";
            } elseif ($filter == 'in-progress') {
                $filterEvent = "in-progress";
            }
        }

        if ($request->isMethod('POST') && $request->request->get('action')) {
            $action = $request->request->get('action');
            $eventId = $request->request->get('event_id');
            $event = $eventRepository->find($eventId);
            $date = $request->request->get('date');
            $bandId = $request->request->get('bandId');
            $band = $bandRepository->find($bandId);
            $hallEventId = $request->request->get('hallId');
            $hallEvent = $hallRepository->find($hallEventId);

            if ($action === 'validate') {
                $status = 1;
                $eventCancel = $eventRepository->getCancelEventsByHallAndDate($hallEvent, $date, $event);
                $bands = [];
                foreach ($eventCancel as $eventUpdate) {
                    $bandEvents = $eventUpdate->getBandEvents();
                    foreach ($bandEvents as $bandEvent) {
                        $bands[] = $bandEvent->getBand();
                        foreach ($bands as $band) {
                            $notification->addNotificationBand("band", $hall->getName(), $band->getId(), "hall", $hall->getId(), "response", $band, 2, $em);
                        }
                    }
                    $eventUpdate->setStatus(2);
                    $em->flush();
                }
            } elseif ($action === 'reject') {
                $status = 2;
            } else {
                $status = 3;
            }

            $event = $eventRepository->find($eventId);
            if ($event) {
                $event->setStatus($status);
                $em->flush();
            }

            $notification->addNotificationBand("band", $hall->getName(), $bandId, "hall", $hall->getId(), "response", $band, $status, $em);
        }

        return $this->render('hall/event.html.twig', [
            'hall' => $hall,
            'eventCome' => $eventCome,
            'eventPast' => $eventPast,
            'band' => $bandFind,
            'dateAdd' => $dateAdd,
            'filterEvent' => $filterEvent
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hall_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hall $hall, HallMemberRepository $hallMemberRepository, EntityManagerInterface $entityManager, AddPhotosService $addPhotosService): Response
    {
        $form = $this->createForm(HallType::class, $hall);
        $form->handleRequest($request);

        $allHallMembers = $hallMemberRepository->findBy(['hall' => $hall]);
        $canAccess = false;

        foreach ($allHallMembers as $hallMember) {
            if ($this->isGranted('hall_member', $hallMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_search_booking', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $img = $form->get('logo')->getData();
            if ($img) {
                $addPhotosService->addNewPicture($img, $hall);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été enregistrées avec succès !');
            return $this->redirectToRoute('app_hall_edit', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hall/edit.html.twig', [
            'hall' => $hall,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/infos', name: 'app_hall_infos', methods: ['GET', 'POST'])]
    public function infos(
        Request $request,
        Hall $hall,
        HallMemberRepository $hallMemberRepository,
        HallInfoRepository $hallInfoRepository,
        EntityManagerInterface $em
    ): Response {
        $allHallMembers = $hallMemberRepository->findBy(['hall' => $hall]);
        $canAccess = false;

        foreach ($allHallMembers as $hallMember) {
            if ($this->isGranted('hall_member', $hallMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_search_booking', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(HallInfoType::class, $hall->getHallInfo());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Vos informations ont été enregistrées avec succès !');

            return $this->redirectToRoute('app_hall_infos', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->render('hall/infos.html.twig', [
            'hall' => $hall,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/event-delete', name: 'app_hall_event_delete', methods: ['POST'])]
    public function deleteEvent(
        Request $request,
        EventRepository $eventRepository,
        NotificationService $notification,
        HallMemberRepository $hallMemberRepository,
        Hall $hall,
        BandEventRepository $bandEventRepository,
        EntityManagerInterface $em
        ): Response
    {
        $allHallMembers = $hallMemberRepository->findBy(['hall' => $hall]);
        $canAccess = false;

        foreach ($allHallMembers as $hallMember) {
            if ($this->isGranted('hall_member', $hallMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_search_booking', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        }

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            if ($action == 'cancelDate'){

                $eventId = $request->request->get('idEvent');
    
    
                $event = $eventRepository->find($eventId);
                $bandEvents = $bandEventRepository->findBy(['event' => $event]);
    
                // dd($bandEvents);
                if ($event){

                    if ($bandEvents) {
                        // dd($bandEvents);
                        foreach($bandEvents as $bandEvent){
                            $bandEvent->setStatus("reject");
                            // $em->remove($bandEvent);
                            $notification->addNotificationHallToBand("band", $hall->getName(), $bandEvent->getBand()->getId(), "hall", $hall->getId(), "cancelDate", $bandEvent->getBand(), $em);

                        }
                    }
                    $event->setStatus(2);
                    $em->flush();
                }
                
            }
        }


        return $this->redirectToRoute('app_hall_event', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}/delete', name: 'app_hall_delete', methods: ['GET', 'POST'])]
    public function delete(
        Security $security,
        Request $request,
        HallMemberRepository $hallMemberRepository,
        HallMemberRoleRepository $hallMemberRoleRepository,
        HallInfoRepository $hallInfoRepository,
        EventRepository $eventRepository,
        BandEventRepository $bandEventRepository,
        Hall $hall,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $security->getUser();

        $allHallMembers = $hallMemberRepository->findBy(['hall' => $hall]);
        $hallInfo = $hallInfoRepository->find($hall);
        $canAccess = false;
        $events = $eventRepository->findBy(['hall' => $hall]);



        foreach ($allHallMembers as $hallMember) {
            if ($this->isGranted('hall_member', $hallMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            $this->addFlash(
                'error',
                'Vous n\'avez pas les droits pour fermer cette Salle'
            );
            return $this->redirectToRoute('app_hall_edit', ["id" => $hall->getId()], Response::HTTP_SEE_OTHER);
        } else {
            foreach ($events as $event) {
                $bandEvents = $bandEventRepository->findBy(['event' => $event]);
                foreach ($bandEvents as $bandEvent) {
                    $entityManager->remove($bandEvent);
                }
                $entityManager->remove($event);
            }
            foreach ($allHallMembers as $hallmember) {
                $roleMember = $hallMemberRoleRepository->findOneBy(['hall_member' => $hallmember->getId()]);
                $entityManager->remove($roleMember);
                $entityManager->remove($hallmember);
            }
            $entityManager->remove($hallInfo);
            $entityManager->remove($hall);
            $entityManager->flush();
            return $this->redirectToRoute('app_profil_show', ["id" => $user->getProfil()->getId()], Response::HTTP_SEE_OTHER);

        }

    }
}
