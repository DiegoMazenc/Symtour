<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogTest extends WebTestCase
{
    public function testLoginAccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testInscription(): void
    {
        $client = static::createClient();

        // Accéder à la page d'inscription
        $crawler = $client->request('GET', '/register');

        // Vérifier que la page est accessible
        $this->assertResponseIsSuccessful();

        // Récupérer le formulaire d'inscription
        $form = $crawler->filter('form[name=registration_form]')->form();

        $form['registration_form[email]'] = 'nouveau_utilisateur@example.com';
        $form['registration_form[plainPassword][first]'] = 'mot_de_passe';
        $form['registration_form[plainPassword][second]'] = 'mot_de_passe';

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier que la réponse est réussie
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
