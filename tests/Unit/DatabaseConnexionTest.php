<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabaseConnectionTest extends KernelTestCase
{
    public function testDatabaseConnection()
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $this->assertInstanceOf('Doctrine\ORM\EntityManagerInterface', $entityManager);
    }
}