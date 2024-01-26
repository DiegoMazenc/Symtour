<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\Event;
use App\Entity\Profil;
use App\Form\BandType;
use App\Entity\BandInfo;
use App\Entity\RoleBand;
use App\Entity\BandMember;
use App\Form\BandMemberType;
use App\Form\SearchFormType;
use App\Form\AddRoleBandType;
use App\Repository\BandRepository;
use App\Repository\EventRepository;
use App\Repository\HallRepository;
use App\Repository\ProfilRepository;
use App\Service\NotificationService;
use App\Repository\RoleBandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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



            return $this->redirectToRoute('app_band_show', ["id" => $band->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('band/new.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_band_show', methods: ['GET','POST'])]
    public function show(Band $band,EntityManagerInterface $em, Request $request, NotificationService $notification): Response
    {
        $notification->isRead((int)$request->query->get('notification_id'));

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $eventId = $request->request->get('event_id');

            if ($action === 'validate') {
                $bandStatus = "validate";
                $status = 1;
            } else  {
                $bandStatus = "reject";
                $status = 2;
            } 

            $event = $em->getRepository(Event::class)->find($eventId);
            if ($event) {
                $event->setBandStatus($bandStatus)
                ->setStatus($status);
                $em->flush();
            }
        }
        return $this->render('band/show.html.twig', [
            'band' => $band,
        ]);
    }

    #[Route('/{id}/members', name: 'app_band_members', methods: ['GET', 'POST'])]
    public function bandMembers(
       NotificationService $notification, Band $band, RoleBandRepository $roleBandRepository, Request $request, ProfilRepository $profilRepository, EntityManagerInterface $em, BandRepository $bandRepository): Response
    {
        $notification->isRead((int)$request->query->get('notification_id'));

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
                ->setRole($roleEntity)
                ->setProfil($profilEntity);

            $em->persist($bandMember);
            $em->flush();


            $notification->addNotificationProfil("profil",$bandName, $profilId, "band", $bandId, "add", $em);
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
    public function event(Band $band, Request $request, BandRepository $bandRepository,HallRepository $hallRepository, EventRepository $eventRepository, EntityManagerInterface $em): Response
    {
      
        $eventCome = $eventRepository->getComeEventsByBand($band);
        $eventsComeValidate = [];
        $eventsComeGuest = [];
        $eventsComeReject = [];
        foreach ($eventCome as $event) {
            $eventsComeValidate[$event->getId()] = [$event];
            $bands = $bandRepository->getBandsByHallAndDate($event);
            $eventsComeValidate[$event->getId()][] = $bands;
        }

        foreach ($eventCome as $event) {
            $eventsComeGuest[$event->getId()] = [$event];
            $bands = $bandRepository->getBandsByHallAndDateGuest($event);
            $eventsComeGuest[$event->getId()][] = $bands;
        }

        foreach ($eventCome as $event) {
            $eventsComeReject[$event->getId()] = [$event];
            $bands = $bandRepository->getBandsByHallAndDateReject($event);
            $eventsComeReject[$event->getId()][] = $bands;
        }
        $eventPast = $eventRepository->getPastEventsByBand($band);
        $dateAdd = null;
        $bandSearch = null;
        $bandConnect = $band->getId();
        if ($request->isMethod('POST')) {
            $formName = $request->request->get('formName');

            if ($formName === 'addBandForm') {
                $addBand = $request->request->get('addBand');
                $dateAdd = $request->request->get('date');
                $bandSearch = $bandRepository->findBySearch($addBand);
            } elseif ($formName === 'inviteBandForm') {
                $bandId = $request->request->get('bandId');
                $bandSearch = $bandRepository->find($bandId);
                $hallId = $request->request->get('hall_id');
                $hallSearch = $hallRepository->find($hallId);

                $date = $request->request->get('date');
                $event = new Event();

                $event
                    ->setHall($hallSearch)
                    ->setBand($bandSearch)
                    ->setDate(new \DateTime($date)) // Assurez-vous de convertir la chaîne en objet DateTime
                    ->setStatus(1)
                    ->setBandStatus("guest");

                $em->persist($event);
                $em->flush();
                
        return $this->redirectToRoute('app_band_event', ["id" => $bandConnect], Response::HTTP_SEE_OTHER);

           
        }
    }

        return $this->render('band/event.html.twig', [
            'band' => $band,
            'eventsComeValidate' => $eventsComeValidate,
            'eventsComeGuest' => $eventsComeGuest,
            'eventsComeReject' => $eventsComeReject,
            'eventPast' => $eventPast,
            'bandSearch' => $bandSearch,
            'dateAdd' => $dateAdd,
            'bandConnect' => $bandConnect,
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


    #[Route('/{id}', name: 'app_band_delete', methods: ['POST'])]
    public function delete(Request $request, Band $band, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $band->getId(), $request->request->get('_token'))) {
            $entityManager->remove($band);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_band_index', [], Response::HTTP_SEE_OTHER);
    }
}
