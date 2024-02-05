<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Event;
use Doctrine\ORM\Events;
use App\Entity\BandMember;
use App\Entity\HallMember;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\HttpFoundation\Request;
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

        // if($entity instanceof BandMember){
        //     $entity->setStatus('guest');
        // }

        // if($entity instanceof HallMember){
        //     $entity->setStatus('guest');
        // }

    }
}
