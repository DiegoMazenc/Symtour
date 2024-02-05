<?php

namespace App\Entity;

use App\Repository\BandMemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(inversedBy: 'bandMembers')]
    private ?Profil $profil = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'band_member', targetEntity: BandMemberRole::class)]
    private Collection $bandMemberRoles;

    public function __construct()
    {
        $this->bandMemberRoles = new ArrayCollection();
    }


   

   
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

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, BandMemberRole>
     */
    public function getBandMemberRoles(): Collection
    {
        return $this->bandMemberRoles;
    }

    public function addBandMemberRole(BandMemberRole $bandMemberRole): static
    {
        if (!$this->bandMemberRoles->contains($bandMemberRole)) {
            $this->bandMemberRoles->add($bandMemberRole);
            $bandMemberRole->setBandMember($this);
        }

        return $this;
    }

    public function removeBandMemberRole(BandMemberRole $bandMemberRole): static
    {
        if ($this->bandMemberRoles->removeElement($bandMemberRole)) {
            // set the owning side to null (unless already changed)
            if ($bandMemberRole->getBandMember() === $this) {
                $bandMemberRole->setBandMember(null);
            }
        }

        return $this;
    }


   
 
}
