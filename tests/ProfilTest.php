<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfilTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/profil/1', );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', 'Mes salles');
    }
}
