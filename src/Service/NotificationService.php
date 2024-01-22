<?php
namespace App\Service;

use App\Entity\Notification;

class NotificationService
{
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
}