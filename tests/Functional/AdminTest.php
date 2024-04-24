<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminTest extends WebTestCase
{
    public function testAccessAdmin(): void
    {
        $client = static::createClient();
        
        // Connexion
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(["id"=> 1]);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/admin/', );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', "Administration");
    }

    public function testDeniedAdmin(): void
    {
        $client = static::createClient();
        
        // Connexion
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(["id"=> 2]);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/admin/', );

        $this->assertResponseStatusCodeSame(403);
    }
}
