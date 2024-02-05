<?php

namespace App\Entity;

use App\Repository\HallMemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(inversedBy: 'hallMembers')]
    private ?Profil $profile = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_create = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'hall_member', targetEntity: HallMemberRole::class)]
    private Collection $hallMemberRoles;

    public function __construct()
    {
        $this->hallMemberRoles = new ArrayCollection();
    }

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
            $hallMemberRole->setHallMember($this);
        }

        return $this;
    }

    public function removeHallMemberRole(HallMemberRole $hallMemberRole): static
    {
        if ($this->hallMemberRoles->removeElement($hallMemberRole)) {
            // set the owning side to null (unless already changed)
            if ($hallMemberRole->getHallMember() === $this) {
                $hallMemberRole->setHallMember(null);
            }
        }

        return $this;
    }



}
