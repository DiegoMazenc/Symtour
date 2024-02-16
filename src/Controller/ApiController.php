<?php


namespace App\Controller;

use App\Entity\Hall;
use App\Repository\BandRepository;
use App\Repository\HallRepository;
use App\Repository\BandMemberRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
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
    public function index(HallRepository $hallRepository, BandMemberRepository $BandMemberRepository): JsonResponse
    {
        $halls = $hallRepository->findAll();
        $bandMembers = $this->security->getUser()->getProfil()->getBandMembers();

        $bandListe = [];
        foreach ($bandMembers as $bandMember) {
            $bandName = $bandMember->getBand();
            $bandListe[] = $bandName;
            
        }
        $responseData = [];

        foreach ($halls as $hall) {
            $data = $hall->jsonSerialize();
            $data['displayedBands'] = $this->getDisplayedBands($hall, $bandListe);
            $responseData[] = $data;
        }
        return $this->json($responseData);
    }

    private function getDisplayedBands(Hall $hall, $bandListe) {
        $displayedBands = [];
        $hallMusics = $hall->getMusicCategory();

        $hallMusicsList = [];
        foreach ($hallMusics as $hallMusic){
            $hallMusicCat = $hallMusic->getId();
            $hallMusicsList[] = $hallMusicCat;
        }

        foreach ($bandListe as $band) {
            if (in_array($band->getMusicCategory()->getId(), $hallMusicsList)){
                $bandName = $band->getName();
                $displayedBands[] = $bandName;
            }
        }

        return $displayedBands;
    }


}
