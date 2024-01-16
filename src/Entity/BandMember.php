<?php

namespace App\Entity;

use App\Repository\BandMemberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BandMemberRepository::class)]
class BandMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bandMembers')]
    private ?Band $band = null;

    #[ORM\ManyToOne]
    private ?RoleBand $role = null;

    #[ORM\ManyToOne(inversedBy: 'bandMembers')]
    private ?Profil $profil = null;

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBand(): ?Band
    {
        return $this->band;
    }

    public function setBand(?Band $band): static
    {
        $this->band = $band;

        return $this;
    }

    public function getRole(): ?RoleBand
    {
        return $this->role;
    }

    public function setRole(?RoleBand $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

 
}
