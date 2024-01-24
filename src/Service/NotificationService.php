<?php
namespace App\Service;

use App\Entity\Profil;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\Routing\RouterInterface;

class NotificationService
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EntityManagerInterface $em
    )
    {

    }
   
    public function addNotificationHall($receipt,$bandName, $idReceipt, $sender, $idSender, $type, $hall, $em){
        
        $notification = new Notification;
        $notification
            ->setMessage("$bandName vous à fait une demande d'event")
            ->setStatus(1)
            ->setDate(new \DateTime())
            ->setReceiptPage($receipt)
            ->setReceiptId($idReceipt)
            ->setSenderPage($sender)
            ->setSenderId($idSender)
            ->setType($type)
            ;

        foreach ($hall->getHallMembers() as $hallMember) {
            $profile = $hallMember->getProfile();

            // La première itération utilise la notification initiale, les suivantes créent des copies
            $notification->setProfil($profile);

            // Cloner la notification pour éviter les références partagées
            $notificationCopy = clone $notification;
            $em->persist($notificationCopy);
        }

        $em->flush();
    }

    public function addNotificationBand($receipt,$hallName, $idReceipt, $sender, $idSender, $type, $band, $status, $em){
        
        $notification = new Notification;
        
        if ($status == 1) {
            $message ="$hallName à accepté votre demande d'event";
            
        } elseif ($status == 2) {
            $message ="$hallName à rejeté votre demande d'event";
           
        } else {
            $message = 'Un problème est survenu';
        }

        $notification
        ->setMessage($message)
        ->setStatus(1)
            ->setDate(new \DateTime())
            ->setReceiptPage($receipt)
            ->setReceiptId($idReceipt)
            ->setSenderPage($sender)
            ->setSenderId($idSender)
            ->setType($type)
            ;

        foreach ($band->getBandMembers() as $bandMember) {
            $profil = $bandMember->getProfil();

            // La première itération utilise la notification initiale, les suivantes créent des copies
            $notification->setProfil($profil);

            // Cloner la notification pour éviter les références partagées
            $notificationCopy = clone $notification;
            $em->persist($notificationCopy);
        }

        $em->flush();
    }

    public function addNotificationProfil($receipt, $SenderName, $idReceipt, $sender, $idSender, $type, $em)
    {
        $notification = new Notification;
    
        $message = "$SenderName Vous invite à les rejoindres";
    
        $profil = $em->getRepository(Profil::class)->find($idReceipt);
    
        $notification
            ->setMessage($message)
            ->setStatus(1)
            ->setDate(new \DateTime())
            ->setReceiptPage($receipt)
            ->setReceiptId($idReceipt)
            ->setSenderPage($sender)
            ->setSenderId($idSender)
            ->setType($type)
            ->setProfil($profil);
    
        $em->persist($notification);
        $em->flush();
    }

    
    public function isRead(
        ?int $notificationId
    )
    {
        if ($notificationId){
            $notification = $this->notificationRepository->find($notificationId);
            $notification->setStatus(2);
            $this->em->flush();
        }
    }
    
}