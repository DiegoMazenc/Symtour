<?php

namespace App\Tests\Controller;

use App\Repository\BandRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BandControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/band/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Band index');
    }

    public function testShowPage()
    {
        $client = static::createClient();

        // Récupérer un groupe et un événement pour le tester
        $bandRepository = $client->getContainer()->get(BandRepository::class);
        $eventRepository = $client->getContainer()->get(EventRepository::class);
    
        // Rechercher un groupe avec au moins un événement associé
        $band = $bandRepository->find(1);;
        $event = $eventRepository->find(1);
    
        // Vérifier si un groupe avec des événements existe dans la base de données
        $this->assertNotNull($band, 'Aucun groupe avec des événements trouvé.');
    
        // Simuler une requête GET pour afficher la page
        $crawler = $client->request('GET', '/band/'.$band->getId());
    
        // Vérifier que la page a été chargée avec succès
        $this->assertResponseIsSuccessful();
    
        // Vérifier que le contenu attendu est présent dans la réponse
        $this->assertSelectorTextContains('h1', $band->getName());
        $this->assertSelectorExists('#event-'.$event->getId()); // Vérifie la présence d'un événement sur la page
    
        // Envoi d'une requête POST pour simuler la validation d'un événement
        $crawler = $client->request('POST', '/band/'.$band->getId(), [
            'action' => 'validate',
            'event_id' => $event->getId(),
            // Autres champs nécessaires pour la validation
        ]);
    
        // Vérifier que la redirection a eu lieu après la validation de l'événement
        $this->assertResponseRedirects('/band/'.$band->getId());
    
        // Récupérer l'événement mis à jour depuis la base de données
        $updatedEvent = $eventRepository->find($event->getId());
    
        // Vérifier que l'état de l'événement a été mis à jour dans la base de données
        $this->assertEquals('validate', $updatedEvent->getStatus());
    
        // Assurez-vous que les autres tests sont effectués pour les autres fonctionnalités, comme le rejet d'un événement, etc.
    }

}
