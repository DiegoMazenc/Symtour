<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Event;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsDoctrineListener(event: Events::prePersist)]
final class InsertListener
{
    
    #[AsEventListener(event: PrePersistEventArgs::class)]
    public function PrePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();
        if($entity instanceof User){
            $entity->setStatus('active')
                ->setRoles(["ROLE_USER"]);
        }

        if($entity instanceof Event){
            $entity->setStatus(3);
        }


    }
}
