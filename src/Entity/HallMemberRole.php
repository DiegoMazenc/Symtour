<?php

namespace App\Entity;

use App\Repository\HallMemberRoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HallMemberRoleRepository::class)]
class HallMemberRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'hallMemberRoles')]
    private ?HallMember $hall_member = null;

    #[ORM\ManyToOne(inversedBy: 'hallMemberRoles')]
    private ?RoleHall $role_hall = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHallMember(): ?HallMember
    {
        return $this->hall_member;
    }

    public function setHallMember(?HallMember $hall_member): static
    {
        $this->hall_member = $hall_member;

        return $this;
    }

    public function getRoleHall(): ?RoleHall
    {
        return $this->role_hall;
    }

    public function setRoleHall(?RoleHall $role_hall): static
    {
        $this->role_hall = $role_hall;

        return $this;
    }
}
