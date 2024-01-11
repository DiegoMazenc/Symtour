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
    private ?band $band = null;

    #[ORM\ManyToOne]
    private ?roleband $role = null;

    #[ORM\ManyToOne(inversedBy: 'bandMembers')]
    private ?profil $profil = null;

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBand(): ?band
    {
        return $this->band;
    }

    public function setBand(?band $band): static
    {
        $this->band = $band;

        return $this;
    }

    public function getRole(): ?roleband
    {
        return $this->role;
    }

    public function setRole(?roleband $role): static
    {
        $this->role = $role;

        return $this;
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

 
}
