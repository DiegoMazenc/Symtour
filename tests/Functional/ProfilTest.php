<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfilTest extends WebTestCase
{
    public function testAccessProfil(): void
    {
        $client = static::createClient();
        
        // Connexion
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(["id"=> 1]);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/profil/1', );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', $user->getProfil()->getPseudo());
    }
}
