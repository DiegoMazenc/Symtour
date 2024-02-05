<?php

namespace App\Entity;

use App\Repository\RoleHallRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleHallRepository::class)]
class RoleHall
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role_name = null;

    #[ORM\OneToMany(mappedBy: 'role_hall', targetEntity: HallMemberRole::class)]
    private Collection $hallMemberRoles;

    public function __construct()
    {
        $this->hallMemberRoles = new ArrayCollection();
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
     * @return Collection<int, HallMemberRole>
     */
    public function getHallMemberRoles(): Collection
    {
        return $this->hallMemberRoles;
    }

    public function addHallMemberRole(HallMemberRole $hallMemberRole): static
    {
        if (!$this->hallMemberRoles->contains($hallMemberRole)) {
            $this->hallMemberRoles->add($hallMemberRole);
            $hallMemberRole->setRoleHall($this);
        }

        return $this;
    }

    public function removeHallMemberRole(HallMemberRole $hallMemberRole): static
    {
        if ($this->hallMemberRoles->removeElement($hallMemberRole)) {
            // set the owning side to null (unless already changed)
            if ($hallMemberRole->getRoleHall() === $this) {
                $hallMemberRole->setRoleHall(null);
            }
        }

        return $this;
    }
}
