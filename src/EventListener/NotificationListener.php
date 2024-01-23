<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Event;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsDoctrineListener(event: Events::postUpdate)]
final class NotificationListener
{
    public function __construct(
        // private Request $request
    )
    {
        
    }
    #[AsEventListener(event: PostUpdateEventArgs::class)]
    public function postUpdate(PostUpdateEventArgs $event): void
    {
        // $entity = $event->getObject();
        // if($entity instanceof Event){
        //     dd($event);        
        // }
    }
}
