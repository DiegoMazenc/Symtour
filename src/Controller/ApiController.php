<?php


namespace App\Controller;

use App\Entity\Band;
use App\Entity\Hall;
use App\Repository\HallRepository;
use App\Repository\EventRepository;
use App\Repository\BandMemberRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    #[Route('/api/search', name: 'app_api_search', methods: ['GET'])]
    public function searchApi(HallRepository $hallRepository, BandMemberRepository $BandMemberRepository): JsonResponse
    {
        $halls = $hallRepository->findByCompleteInfo();
        $bandMembers = $this->security->getUser()->getProfil()->getBandMembers();

        $bandListe = [];
        foreach ($bandMembers as $bandMember) {
            if ($bandMember->getStatus() == 'admin' || $bandMember->getStatus() == 'member') {
                $bandName = $bandMember->getBand();
                $bandListe[] = $bandName;
            }
        }

        $responseData = [];

        foreach ($halls as $hall) {
            $data = $hall->jsonSerialize();
            $data['displayedBands'] = $this->getDisplayedBands($hall, $bandListe);

            $events = $hall->getEvents();
            $eventListe = [];
            $nbrEvent = 0;
            foreach ($events as $event) {
                $eventDate = $event->getDate();
                $formattedEventDate = $eventDate->format('Y-m-d');
                $nbrEvent += $event->getStatus() == 1 ? 1 : 0;
                if ($event->getStatus() == 1 && new \DateTime() < $eventDate) {
                    $eventListe[] = $formattedEventDate;

                }
            }
            $data['eventListe'] = $eventListe;
            $data['nbrEvent'] = $nbrEvent;

            $responseData[] = $data;
        }

        return $this->json($responseData);
    }

    private function getDisplayedBands(Hall $hall, $bandListe)
    {
        $displayedBands = [];
        $hallMusics = $hall->getMusicCategory();

        $hallMusicsList = [];
        foreach ($hallMusics as $hallMusic) {
            $hallMusicCat = $hallMusic->getId();
            $hallMusicsList[] = $hallMusicCat;
        }

        foreach ($bandListe as $band) {
            if (in_array($band->getMusicCategory()->getId(), $hallMusicsList)) {
                $bandName = $band->getName();
                $displayedBands[] = $bandName;
            }
        }

        return $displayedBands;
    }

    #[Route('/api/booking/{id}', name: 'app_api_booking', methods: ['GET'])]
    public function bookingApi(EventRepository $eventRepository, Hall $hall): JsonResponse
    {
        $bandMembers = $this->security->getUser()->getProfil()->getBandMembers();

        $eventCome = $eventRepository->getComeEventsByHallAsc($hall);
        $eventData = [];
        $currentDate = new \DateTime();
        foreach ($bandMembers as $bandMember) {
            foreach ($bandMember->getBand()->getBandEvents() as $event) {
                if ($event->getEvent()->getStatus() !== 2 && $event->getStatus() !== 'reject' && $event->getEvent()->getDate() > $currentDate) {
                    $BandEvents = [
                        'dateBandEvent' => $event->getEvent()->getDate()->format('Y-m-d'),
                        'bandName' => $bandMember->getBand()->getName(),
                        'eventStatus' => $event->getEvent()->getStatus(),
                        'bandStatus' => $event->getStatus()
                    ];
                    $eventData[] = $BandEvents;
                }
            }
        }
        foreach ($eventCome as $event) {

            $data = [
                'date' => $event->getDate()->format('Y-m-d'),
                'statusDate' => strval($event->getStatus()),
            ];
            $eventData[] = $data;
        }

        return $this->json($eventData);
    }

    #[Route('/api/band-event/{id}', name: 'app_api_band_event', methods: ['GET'])]
    public function bandEventgApi(EventRepository $eventRepository, Band $band): JsonResponse
    {
        $eventAll = $eventRepository->getAllEventsByBand($band);
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
        return $this->json($eventsData);
    }

    #[Route('/api/hall-event/{id}', name: 'app_api_hall_event', methods: ['GET'])]
    public function hallEventgApi(EventRepository $eventRepository, Hall $hall): JsonResponse
    {
        $eventAll = $eventRepository->getAllEventsByHall($hall);
        $eventsData = [];
        foreach ($eventAll as $event) {
            // Ignorer les événements avec un statut de 2 (rejeté)
            if ($event->getStatus() != 2) {
                $eventData = [
                    'date' => $event->getDate()->format('Y-m-d'),
                    'statusDate' => $event->getStatus(),
                    'bands' => [],
                ];

                foreach ($event->getBandEvents() as $bandEvent) {
                    $bandData = [
                        'name' => $bandEvent->getBand()->getName(),
                        'logo' => $bandEvent->getBand()->getLogo(),
                        'music' => $bandEvent->getBand()->getMusicCategory()->getCategory(),
                        'style' => $bandEvent->getBand()->getDefineStyle(),
                        'status' => $bandEvent->getStatus(),
                    ];
                    $eventData['bands'][] = $bandData;
                }

                $eventsData[] = $eventData;
            }
        }
        return $this->json($eventsData);
    }


}


