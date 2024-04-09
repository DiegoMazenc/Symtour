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
    ) {
    }

    public function addNotificationHall($receipt, $bandName, $idReceipt, $sender, $idSender, $type, $hall, $em)
    {

        $uniqueProfiles = [];
        if ($type == "event"){
            $message = "$bandName vous à fait une demande d'event";
        } else if ($type == "cancel"){
            $message = "$bandName à annulé sa demande d'event";
        }
    
        foreach ($hall->getHallMembers() as $hallMember) {
            $profil = $hallMember->getProfile();
            $uniqueProfiles[$profil->getId()] = $profil;
        }
    
        foreach ($uniqueProfiles as $profil) {
            $notification = new Notification;
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
        }
    
        $em->flush();
    }

    public function addNotificationBand($receipt, $hallName, $idReceipt, $sender, $idSender, $type, $band, $status, $em)
    {
        $uniqueProfiles = [];
    
        foreach ($band->getBandMembers() as $bandMember) {
            $profil = $bandMember->getProfil();
            $uniqueProfiles[$profil->getId()] = $profil;
        }
    
        foreach ($uniqueProfiles as $profil) {
            $notification = new Notification;
       

        if ($status == 1) {
            $message = "$hallName à accepté votre demande d'event";
        } elseif ($status == 2) {
            $message = "$hallName à rejeté votre demande d'event";
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
            ->setProfil($profil);
    
            $em->persist($notification);
        }
    
        $em->flush();
    }

    public function addNotificationProfilToBand($receipt, $ProfilName, $idReceipt, $sender, $idSender, $type, $band, $status, $em)
    {

        $uniqueProfiles = [];
    
        foreach ($band->getBandMembers() as $bandMember) {
            $profil = $bandMember->getProfil();
            $uniqueProfiles[$profil->getId()] = $profil;
        }
    
        foreach ($uniqueProfiles as $profil) {
            $notification = new Notification;
       

        if ($status == "member") {
            $message = "$ProfilName à accepté votre invitation";
        } elseif ($status == "reject") {
            $message = "$ProfilName à rejeté votre invitation";
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
            ->setProfil($profil);
    
            $em->persist($notification);
        }
    
        $em->flush();
    }

    public function addNotificationHallToBand($receipt, $ProfilName, $idReceipt, $sender, $idSender, $type, $band, $em)
    {

        $uniqueProfiles = [];
    
        foreach ($band->getBandMembers() as $bandMember) {
            $profil = $bandMember->getProfil();
            $uniqueProfiles[$profil->getId()] = $profil;
        }
    
        foreach ($uniqueProfiles as $profil) {
            $notification = new Notification;
       

       
            $message = "$ProfilName vous invite à un évènement";
        

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
        }
    
        $em->flush();
    }

    public function addNotificationBandToBand($receipt, $ProfilName, $idReceipt, $sender, $idSender, $type, $band, $em)
    {

        $uniqueProfiles = [];
    
        foreach ($band->getBandMembers() as $bandMember) {
            $profil = $bandMember->getProfil();
            $uniqueProfiles[$profil->getId()] = $profil;
        }
    
        foreach ($uniqueProfiles as $profil) {
            $notification = new Notification;
       

       
            $message = "$ProfilName vous invite à un évènement";
        

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
        }
    
        $em->flush();
    }


    public function addNotificationBandToHall($receipt, $ProfilName, $idReceipt, $sender, $idSender, $type, $hall, $em)
    {

        $uniqueProfiles = [];
    
        foreach ($hall->getHallMembers() as $hallMember) {
            $profil = $hallMember->getProfile();
            $uniqueProfiles[$profil->getId()] = $profil;
        }
    
        foreach ($uniqueProfiles as $profil) {
            $notification = new Notification;
       

       if($type == "reject"){
            $message = "$ProfilName a rejeté votre proposition";
       }elseif($type == "validate"){
        $message = "$ProfilName a accepté votre proposition";
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
            ->setProfil($profil);
    
            $em->persist($notification);
        }
    
        $em->flush();
    }

 

    public function addNotificationProfilToHall($receipt, $ProfilName, $idReceipt, $sender, $idSender, $type, $hall, $status, $em)
    {
        $uniqueProfiles = [];
    
        foreach ($hall->getHallMembers() as $hallMember) {
            $profil = $hallMember->getProfile();
            $uniqueProfiles[$profil->getId()] = $profil;
        }
    
        foreach ($uniqueProfiles as $profil) {
            $notification = new Notification;
    
            if ($status == "member") {
                $message = "$ProfilName a accepté votre invitation";
            } elseif ($status == "reject") {
                $message = "$ProfilName a rejeté votre invitation";
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
                ->setProfil($profil);
    
            $em->persist($notification);
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
    ) {
        if ($notificationId) {
            $notification = $this->notificationRepository->find($notificationId);
            $notification->setStatus(2);
            $this->em->flush();
        }
    }
}
