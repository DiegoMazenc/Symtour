<?php

namespace App\Entity;

use App\Repository\HallMemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HallMemberRepository::class)]
class HallMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'hallMembers')]
    private ?Hall $hall = null;

    #[ORM\ManyToOne]
    private ?RoleHall $role = null;

    #[ORM\ManyToOne(inversedBy: 'hallMembers')]
    private ?Profil $profile = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_create = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHall(): ?Hall
    {
        return $this->hall;
    }

    public function setHall(?Hall $hall): static
    {
        $this->hall = $hall;

        return $this;
    }

    public function getRole(): ?RoleHall
    {
        return $this->role;
    }

    public function setRole(?RoleHall $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getProfile(): ?Profil
    {
        return $this->profile;
    }

    public function setProfile(?Profil $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->date_create;
    }

    public function setDateCreate(?\DateTimeInterface $date_create): static
    {
        $this->date_create = $date_create;

        return $this;
    }


}
