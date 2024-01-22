<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?profil $profil = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    private ?int $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $receipt_page = null;

    #[ORM\Column(nullable: true)]
    private ?int $receipt_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sender_page = null;

    #[ORM\Column(nullable: true)]
    private ?int $sender_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfil(): ?profil
    {
        return $this->profil;
    }

    public function setProfil(?profil $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getReceiptPage(): ?string
    {
        return $this->receipt_page;
    }

    public function setReceiptPage(?string $receipt_page): static
    {
        $this->receipt_page = $receipt_page;

        return $this;
    }

    public function getReceiptId(): ?int
    {
        return $this->receipt_id;
    }

    public function setReceiptId(?int $receipt_id): static
    {
        $this->receipt_id = $receipt_id;

        return $this;
    }

    public function getSenderPage(): ?string
    {
        return $this->sender_page;
    }

    public function setSenderPage(?string $sender_page): static
    {
        $this->sender_page = $sender_page;

        return $this;
    }

    public function getSenderId(): ?int
    {
        return $this->sender_id;
    }

    public function setSenderId(?int $sender_id): static
    {
        $this->sender_id = $sender_id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
