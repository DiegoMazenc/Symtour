<?php

namespace App\Entity;

use App\Repository\RoleBandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleBandRepository::class)]
class RoleBand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role_name = null;

    #[ORM\OneToMany(mappedBy: 'role_band', targetEntity: BandMemberRole::class)]
    private Collection $bandMemberRoles;

    public function __construct()
    {
        $this->bandMemberRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleName(): ?string
    {
        return $this->role_name;
    }

    public function setRoleName(?string $role_name): static
    {
        $this->role_name = $role_name;

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
            $bandMemberRole->setRoleBand($this);
        }

        return $this;
    }

    public function removeBandMemberRole(BandMemberRole $bandMemberRole): static
    {
        if ($this->bandMemberRoles->removeElement($bandMemberRole)) {
            // set the owning side to null (unless already changed)
            if ($bandMemberRole->getRoleBand() === $this) {
                $bandMemberRole->setRoleBand(null);
            }
        }

        return $this;
    }
}
