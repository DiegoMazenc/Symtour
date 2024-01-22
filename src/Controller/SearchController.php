<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\SearchFormType;
use App\Entity\Band;
use App\Entity\Notification;
use App\Form\FilterSearchType;
use App\Repository\MusicCategoryRepository;
use App\Repository\HallRepository;
use App\Service\NotificationService;
use App\Service\TestService;
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
        $filter = $this->createForm(FilterSearchType::class);
        $filter->handleRequest($request);


        $halls = $hallRepository->findAll();
        if ($filter->isSubmitted() && $filter->isValid()) {
            $halls = $hallRepository->filter($filter->getData());
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchData = $searchForm->getData();
            $halls = $hallRepository->findBySearch($searchData['search']);
        }
        // dd($filter->createView()->children);
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'halls' => $halls,
            'music_categories' => $musicCategoryRepository->findAll(),
            'filterForm' => $filter->createView(),
            'searchForm' => $searchForm->createView()

        ]);
    }

    #[Route('/search/booking/{id}', name: 'app_search_booking', methods: ['GET', 'POST'])]
    public function booking(Request $request, $id,NotificationService $notification, HallRepository $hallRepository, EntityManagerInterface $em): Response
    {
        $date = $request->query->get('date');
        $hall = $hallRepository->find($id);
        $dateBooking = $request->request->get('booking_date');
        $bandId = $request->request->get('band_id');

        if ($request->isMethod('POST')) {
            // Vous devez remplacer 'Your\Entity\Namespace\Band' par la classe réelle de votre entité Band
            $band = $em->getRepository(Band::class)->find($bandId);
            $bandName = $band->getName();
            $event = new Event();
            $event->setHall($hall)
                ->setBand($band)
                ->setDate(new \DateTime($dateBooking))
                ->setStatus(3);

            $em->persist($event);
            $em->flush();

            $receipt = "hall";
            $sender = "band";
            $type = "event";
            $notification->addNotificationHall($receipt,$bandName, $id, $sender, $bandId, $type, $hall, $em);
        }

        return $this->render('search/booking.html.twig', [
            'hall' => $hall,
            'date' => $date,
        ]);
    }
}
