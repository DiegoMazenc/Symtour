<?php


namespace App\Controller;

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
        $halls = $hallRepository->findAll();
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
        $eventCome = $eventRepository->getComeEventsByHallAsc($hall);
        $eventData = [];
        foreach ($eventCome as $event){
            $data = [
                'date' => $event->getDate()->format('Y-m-d'),
                'statusDate' => strval($event->getStatus())
            ];
            $eventData[] = $data;
        }

        return $this->json($eventData);
    }


}


