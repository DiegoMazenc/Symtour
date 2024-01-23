<?php

namespace App\EventListener;
use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
#[AsDoctrineListener(event: Events::prePersist)]
final class InsertListener
{
    // #[AsEventListener(event: PrePersistEventArgs::class)]
    // public function onPrePersist(PrePersistEventArgs $event): void
    // {
    //     $entity = $event->getEntity();
    //     if($entity instanceof User){
    //         $entity->setStatus('active');
    //     }
    // }
}
