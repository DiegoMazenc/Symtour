<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\Event;
use App\Entity\Profil;
use App\Form\BandType;
use App\Entity\BandInfo;
use App\Entity\RoleBand;
use App\Entity\BandEvent;
use App\Entity\BandMember;
use App\Form\BandInfoType;
use App\Form\BandMemberType;
use App\Form\SearchFormType;
use App\Form\AddRoleBandType;
use App\Entity\BandMemberRole;
use App\Service\AddPhotosService;
use App\Repository\BandRepository;
use App\Repository\HallRepository;
use App\Repository\EventRepository;
use App\Repository\ProfilRepository;
use App\Service\NotificationService;
use App\Repository\BandInfoRepository;
use App\Repository\RoleBandRepository;
use App\Repository\BandEventRepository;
use App\Repository\BandMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BandMemberRoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->getData()->getLogo() == null){
                $band->setLogo('/assets/img/profil_band.png');

           }
            $entityManager->persist($band);
            $entityManager->flush();
            $bandMember = new BandMember();
            $profil = $entityManager->getRepository(Profil::class)->findBy(["IdUser" => $token->getUser()]);
            $defaultRoleId = 1;
            $defaultRole = $entityManager->getRepository(RoleBand::class)->find($defaultRoleId);

            $bandMember->setBand($band)
                ->setProfil($profil[0])
                ->setStatus("admin");
            $entityManager->persist($bandMember);
            $entityManager->flush();

            $bandInfo = new BandInfo();
            $bandInfo->setBandId($band);
            $entityManager->persist($bandInfo);
            $entityManager->flush();


            $bandMemberRole = new BandMemberRole();
            $bandMemberRole
                ->setBandMember($bandMember)
                ->setRoleBand($defaultRole);
            $entityManager->persist($bandMemberRole);
            $entityManager->flush();

            return $this->redirectToRoute('app_band_show', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('band/new.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_band_show', methods: ['GET', 'POST'])]
    public function show(
        Band $band,
        EntityManagerInterface $em,
        EventRepository $eventRepository,
        Request $request,
        HallRepository $hallRepository,
        NotificationService $notification
    ): Response {
        $eventCome = $eventRepository->getComeEventsByBand($band);
        $eventPast = $eventRepository->getPastEventsByBandForShow($band);
        $eventAll = $eventRepository->getAllEventsByBand($band);
        $notification->isRead((int)$request->query->get('notification_id'));

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $eventId = $request->request->get('event_id');
            $hallId = $request->request->get('hallId');
            $hall = $hallRepository->find((int)$hallId);

            if ($action === 'validate') {
                $status = "validate";
            } else {
                $status = "reject";
            }

            $bandEvent = $em->getRepository(BandEvent::class)->find($eventId);
            if ($bandEvent) {
                $bandEvent->setStatus($status);
                $em->flush();

                $notification->addNotificationBandToHall("hall", $band->getName(), $hallId, "band", $band->getId(), $status, $hall, $em);
            }
        }

        $eventsData = [];
        foreach ($eventAll as $event) {
            // Ignorer les événements avec un statut de 2 (rejeté)
            if ($event->getStatus() != 2) {
                $eventData = [
                    'date' => $event->getDate()->format('Y-m-d'),
                    'halls' => [
                        [
                            'name' => $event->getHall()->getName(),
                            'logo' => $event->getHall()->getLogo(),
                            'city' => $event->getHall()->getHallInfo()->getCity(),
                            'status' => $event->getStatus(),
                        ]
                    ],
                ];
    
                $eventsData[] = $eventData;
            }
        }
        
        return $this->render('band/show.html.twig', [
            'band' => $band,
            'eventCome' => $eventCome,
            'eventPast' => $eventPast,
            'allEvent' => $eventAll,
            'eventsData' => json_encode($eventsData),
        ]);
    }

    #[Route('/{id}/members', name: 'app_band_members', methods: ['GET', 'POST'])]
    public function bandMembers(
        NotificationService $notification,
        Band $band,
        RoleBandRepository $roleBandRepository,
        Request $request,
        ProfilRepository $profilRepository,
        EntityManagerInterface $em,
        BandMemberRepository $bandMemberRepository,
        BandMemberRoleRepository $bandMemberRoleRepository,
        BandRepository $bandRepository
    ): Response {
        $notification->isRead((int)$request->query->get('notification_id'));

        $allBandMembers = $bandMemberRepository->findBy(['band' => $band]);
        $canAccess = false;

        foreach ($allBandMembers as $bandMember) {
            if ($this->isGranted('band_member', $bandMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_band_show', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        }

        $roles = $roleBandRepository->findAll();

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $profil = '';

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchData = $searchForm->getData();
            $profil = $profilRepository->findBySearch($searchForm->isSubmitted() && $searchForm->isValid() ? $searchData['search'] : null);
        }

        $addRoleBand = $this->createForm(AddRoleBandType::class);
        $addRoleBand->handleRequest($request);
        $bandName = $band->getName();

        if ($addRoleBand->isSubmitted() && $addRoleBand->isValid()) {
            $addRoleBandData = $addRoleBand->getData();
            $bandEntity = $bandRepository->find($addRoleBandData['band']);
            $bandId = $bandEntity->getId();
            $roleEntity = $roleBandRepository->find($addRoleBandData['role']);
            $profilEntity = $profilRepository->find($addRoleBandData['profil']);
            $profilId = $profilEntity->getId();

            $bandMember = new BandMember();
            $bandMember
                ->setBand($bandEntity)
                ->setProfil($profilEntity)
                ->setStatus("guest");

            $em->persist($bandMember);

            $bandMemberRole = new BandMemberRole();
            $bandMemberRole->setBandMember($bandMember)
                ->setRoleBand($roleEntity);
            $em->persist($bandMemberRole);

            $em->flush();

            $notification->addNotificationProfil("profil", $bandName, $profilId, "band", $bandId, "add", $em);
        }

        if ($request->isMethod('POST')) {
            $member = $request->request->get('memberId');

            foreach ($request->request->all() as $key => $value) {
                if (strpos($key, 'role_') !== false) {
                    $idRole = $value;
                    $role = $roleBandRepository->find((int)$idRole);
                    $idMemberRoleBand = $request->request->get("idMemberRoleBand_" . substr($key, 5));
                    $MemberRoleBand = $bandMemberRoleRepository->find((int)$idMemberRoleBand);

                    if ($MemberRoleBand) {
                        $MemberRoleBand->setRoleBand($role);
                    }
                }

                if ($request->request->has("deleteRole_" . substr($key, 5))) {
                    $idMemberRoleBandToDelete = $request->request->get("idMemberRoleBand_" . substr($key, 5));
                    $memberRoleToDelete = $bandMemberRoleRepository->find((int)$idMemberRoleBandToDelete);

                    if ($memberRoleToDelete) {
                        $em->remove($memberRoleToDelete);
                        $em->flush();
                    }
                }
            }

            $status = $request->request->get('status');
            $bandMember = $bandMemberRepository->find((int)$member);

            if ($bandMember) {
                $band = $bandMember->getBand();
                $countAdmin = $bandMemberRepository->countAdmin($band, 'admin');

                if ($status != "admin" && $countAdmin < 2) {
                    $this->addFlash('denied', 'Vous devez élire un nouvel Admin !');
                } else {
                    $bandMember->setStatus($status);
                    $em->persist($bandMember);
                    $em->flush();
                }
            }

            $newRole = $request->request->get('newRole');
            $role = $roleBandRepository->find((int)$newRole);

            if ($newRole != "1") {
                $newMemberRoleBand = new BandMemberRole;

                $newMemberRoleBand
                    ->setBandMember($bandMember)
                    ->setRoleBand($role);

                $em->persist($newMemberRoleBand);
                $em->flush();
            }

            if ($request->request->has("deleteMember")) {
                $idMember = $request->request->get('idMember');
                $member = $bandMemberRepository->find($idMember);

                if ($member) {
                    $bandMemberRoles = $bandMemberRoleRepository->findBy(['band_member' => $member]);

                    foreach ($bandMemberRoles as $bandMemberRole) {
                        $em->remove($bandMemberRole);
                    }

                    $em->flush();

                    $em->remove($member);
                    $em->flush();
                }
            }
        }

        return $this->render('band/members.html.twig', [
            'band' => $band,
            'searchForm' => $searchForm->createView(),
            'addRoleBand' => $addRoleBand->createView(),
            'profil' => $profil,
            'roles' => $roles
        ]);
    }



    #[Route('/{id}/event', name: 'app_band_event', methods: ['GET', 'POST'])]
    public function event(
        Band $band,
        Request $request,
        NotificationService $notification,
        HallRepository $hallRepository,
        BandRepository $bandRepository,
        BandEventRepository $bandEventRepository,
        EventRepository $eventRepository,
        BandMemberRepository $bandMemberRepository,
        EntityManagerInterface $em
    ): Response {
        $allBandMembers = $bandMemberRepository->findBy(['band' => $band]);
        $canAccess = false;

        foreach ($allBandMembers as $bandMember) {
            if ($this->isGranted('band_member', $bandMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_band_show', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        }


        $eventCome = $eventRepository->getComeEventsByBand($band);
        $eventPast = $eventRepository->getPastEventsByBand($band);

        $idBandConnect = $band->getId();
        $dateAdd = null;
        $bandFind = null;
        $filterEvent = "all";

        if ($request->isMethod('POST')) {
            $formName = $request->request->get('formName');

            if ($formName === 'addBandForm') {
                $addBand = $request->request->get('addBand');
                $dateAdd = $request->request->get('date');
                $bandFind = $bandRepository->findBySearch($addBand);
            } elseif ($formName === 'inviteBandForm') {

                $bandId = $request->request->get('bandId');
                $bandGuest = $bandRepository->find($bandId);
                $eventId = $request->request->get('eventId');
                $event = $eventRepository->find($eventId);

                $bandEvent = new BandEvent();

                $bandEvent
                    ->setEvent($event)
                    ->setBand($bandGuest)
                    ->setStatus("guest");

                $em->persist($bandEvent);
                $em->flush();

                $notification->addNotificationBandToBand("band", $band->getName(), $bandGuest->getId(), "band", $band->getId(), "guest", $bandGuest, $em);

                return $this->redirectToRoute('app_band_event', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        if ($request->isMethod('POST') && $request->request->get('action')) {
            $action = $request->request->get('action');
            $eventId = $request->request->get('event_id');
            $hallId = $request->request->get('hallId');
            $bandEventId = $request->request->get('bandEvent_id');
            $hall = $hallRepository->find((int)$hallId);

            if ($action === 'cancel') {
                $notification->addNotificationHall("hall", $band->getName(), $hall->getId(), "band", $band->getId(), $action, $hall, $em);
                $bandEvent = $bandEventRepository->find($bandEventId);
                if ($bandEvent) {
                    $em->remove($bandEvent);
                    $em->flush();
                }

                $event = $eventRepository->find($eventId);
                if ($event) {
                    $em->remove($event);
                    $em->flush();
                } else {
                    dd($event);
                }
            }
        }

        if ($request->isMethod('POST')) {
            $filter = $request->request->get('filter');

            if ($filter == 'all') {
                $filterEvent = "all";
            } elseif ($filter == 'valid') {
                $filterEvent = "valid";
            } elseif ($filter == 'in-progress') {
                $filterEvent = "in-progress";
            }
        }

        return $this->render('band/event.html.twig', [

            'idBandConnect' => $idBandConnect,
            'eventCome' => $eventCome,
            'band' => $band,
            'eventPast' => $eventPast,
            'bandFind' => $bandFind,
            'dateAdd' => $dateAdd,
            'filterEvent' => $filterEvent,
        ]);
    }

    #[Route('/{id}/event-delete', name: 'app_band_event_delete', methods: ['POST'])]
    public function deleteEvent(Request $request, BandMemberRepository $bandMemberRepository, Band $band, BandEventRepository $bandEventRepository, EntityManagerInterface $entityManager): Response

    {
        $allBandMembers = $bandMemberRepository->findBy(['band' => $band]);
        $canAccess = false;

        foreach ($allBandMembers as $bandMember) {
            if ($this->isGranted('band_member', $bandMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_band_show', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        }

        if ($request->isMethod('POST')) {
            $eventId = $request->request->get('idEvent');

            $bandEvent = $bandEventRepository->find($eventId);

            if ($bandEvent) {
                $entityManager->remove($bandEvent);
                $entityManager->flush();
            }
        }


        return $this->redirectToRoute('app_band_event', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}/edit', name: 'app_band_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Band $band, BandMemberRepository $bandMemberRepository, EntityManagerInterface $entityManager, AddPhotosService $addPhotosService): Response
    {

        $allBandMembers = $bandMemberRepository->findBy(['band' => $band]);
        $canAccess = false;

        foreach ($allBandMembers as $bandMember) {
            if ($this->isGranted('band_member', $bandMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_band_show', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('logo')->getData();
            if ($img) {
                $addPhotosService->addNewPicture($img, $band);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_band_edit', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('band/edit.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/infos', name: 'app_band_infos', methods: ['GET', 'POST'])]
    public function infos(
        Request $request,
        Band $band,
        BandMemberRepository $bandMemberRepository,
        EntityManagerInterface $em
    ): Response {
        $allBandMembers = $bandMemberRepository->findBy(['band' => $band]);
        $canAccess = false;

        foreach ($allBandMembers as $bandMember) {
            if ($this->isGranted('band_member', $bandMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            return $this->redirectToRoute('app_band_show', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(BandInfoType::class, $band->getBandInfo());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Vos informations ont été enregistrées avec succès !');

            return $this->redirectToRoute('app_band_infos', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);

        } 

        return $this->render('band/infos.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/delete', name: 'app_band_delete', methods: ['GET', 'POST'])]
    public function delete(
        Security $security,
        Request $request,
            BandMemberRepository $bandMemberRepository,
            BandMemberRoleRepository $bandMemberRoleRepository,
            BandInfoRepository $bandInfoRepository,
            Band $band,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $security->getUser();

        $allBandMembers = $bandMemberRepository->findBy(['band' => $band]);
        $bandInfo = $bandInfoRepository->findOneBy(['bandId' => $band->getId()]);
        $canAccess = false;

        foreach ($allBandMembers as $bandMember) {
            if ($this->isGranted('band_member', $bandMember)) {
                $canAccess = true;
                break;
            }
        }

        if (!$canAccess) {
            $this->addFlash(
               'error',
               'Vous n\'avez pas les droits pour supprimer ce groupe'
            );
            return $this->redirectToRoute('app_band_edit', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        } else {
            foreach ($allBandMembers as $bandMember) {
                $roleMember = $bandMemberRoleRepository->findOneBy(['band_member' => $bandMember->getId()]);
                $entityManager->remove($roleMember);
                $entityManager->remove($bandMember);
            }
            $entityManager->remove($bandInfo);
            $entityManager->remove($band);
            $entityManager->flush();
            return $this->redirectToRoute('app_profil_show', ["id" => $user->getId()], Response::HTTP_SEE_OTHER);

        }

    }
}
