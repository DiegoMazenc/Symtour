<?php

namespace App\Entity;

use App\Repository\BandMemberRoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BandMemberRoleRepository::class)]
class BandMemberRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bandMemberRoles')]
    private ?BandMember $band_member = null;

    #[ORM\ManyToOne(inversedBy: 'bandMemberRoles')]
    private ?RoleBand $role_band = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBandMember(): ?BandMember
    {
        return $this->band_member;
    }

    public function setBandMember(?BandMember $band_member): static
    {
        $this->band_member = $band_member;

        return $this;
    }

    public function getRoleBand(): ?RoleBand
    {
        return $this->role_band;
    }

    public function setRoleBand(?RoleBand $role_band): static
    {
        $this->role_band = $role_band;

        return $this;
    }
}
