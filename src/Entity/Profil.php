<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    // private ?User $id_user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(nullable: true)]
    private ?int $zip_code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: BandMember::class)]
    private Collection $bandMembers;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: HallMember::class)]
    private Collection $hallMembers;

    #[ORM\OneToOne(inversedBy: 'profil', cascade: ['persist', 'remove'])]
    private ?User $IdUser = null;

    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: Notification::class)]
    private Collection $notifications;



    public function __construct()
    {
        $this->bandMembers = new ArrayCollection();
        $this->hallMembers = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->IdUser;
    }

    public function setIdUser(?User $IdUser): static
    {
        $this->IdUser = $IdUser;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zip_code;
    }

    public function setZipCode(?int $zip_code): static
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, BandMember>
     */
    public function getBandMembers(): Collection
    {
        return $this->bandMembers;
    }

    public function addBandMember(BandMember $bandMember): static
    {
        if (!$this->bandMembers->contains($bandMember)) {
            $this->bandMembers->add($bandMember);
            $bandMember->setProfil($this);
        }

        return $this;
    }

    public function removeBandMember(BandMember $bandMember): static
    {
        if ($this->bandMembers->removeElement($bandMember)) {
            // set the owning side to null (unless already changed)
            if ($bandMember->getProfil() === $this) {
                $bandMember->setProfil(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HallMember>
     */
    public function getHallMembers(): Collection
    {
        return $this->hallMembers;
    }

    public function addHallMember(HallMember $hallMember): static
    {
        if (!$this->hallMembers->contains($hallMember)) {
            $this->hallMembers->add($hallMember);
            $hallMember->setProfile($this);
        }

        return $this;
    }

    public function removeHallMember(HallMember $hallMember): static
    {
        if ($this->hallMembers->removeElement($hallMember)) {
            // set the owning side to null (unless already changed)
            if ($hallMember->getProfile() === $this) {
                $hallMember->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setProfil($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getProfil() === $this) {
                $notification->setProfil(null);
            }
        }

        return $this;
    }




}
