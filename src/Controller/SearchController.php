<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Band;
use App\Entity\BandEvent;
use App\Repository\EventRepository;
use App\Repository\MusicCategoryRepository;
use App\Repository\HallRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET', 'POST'])]
    public function index(Request $request, HallRepository $hallRepository, MusicCategoryRepository $musicCategoryRepository): Response
    {

        return $this->render('search/index.html.twig', [
            'music_categories' => $musicCategoryRepository->findAll(),
        ]);
    }

    #[Route('/search/booking/{id}', name: 'app_search_booking', methods: ['GET', 'POST'])]
    public function booking(Request $request, $id, NotificationService $notification, EventRepository $eventRepository, HallRepository $hallRepository, EntityManagerInterface $em): Response
    {

        $date = $request->query->get('date');
        $hall = $hallRepository->find($id);
        $dateBooking = $request->request->get('booking_date');
        $dateTimeBooking = new \DateTime($dateBooking);
        $formatDateBooking = $dateTimeBooking->format('Y-m-d');
        $bandId = $request->request->get('band_id');

        if ($request->isMethod('POST')) {

            $band = $em->getRepository(Band::class)->find($bandId);
            $bandName = $band->getName();
            $event = new Event();
            $event->setHall($hall)
                ->setDate(new \DateTime($formatDateBooking))
                ->setStatus(3);

            $em->persist($event);
            $em->flush();
            $bandEvent = new BandEvent();
            $bandEvent->setBand($band)
                ->setEvent($event)
                ->setStatus("validate");
            $em->persist($bandEvent);
            $em->flush();

            $notification->addNotificationHall("hall", $bandName, $id, "band", $bandId, "event", $hall, $em);
        }

        return $this->render('search/booking.html.twig', [
            'hall' => $hall,
            'date' => $date,

        ]);
    }
}
  