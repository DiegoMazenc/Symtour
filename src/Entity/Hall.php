<?php

namespace App\Entity;

use App\Repository\HallRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HallRepository::class)]
class Hall
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $logo = null;


    #[ORM\OneToMany(mappedBy: 'hall', targetEntity: HallMember::class)]
    private Collection $hallMembers;

    #[ORM\ManyToMany(targetEntity: MusicCategory::class, inversedBy: 'halls')]
    private Collection $music_category;

    #[ORM\OneToMany(mappedBy: 'hall', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $structure = null;

    #[ORM\OneToMany(mappedBy: 'hall', targetEntity: HallInfo::class)]
    private Collection $hallInfos;

    public function __construct()
    {
        $this->hallMembers = new ArrayCollection();
        $this->music_category = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->hallInfos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

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
            $hallMember->setHall($this);
        }

        return $this;
    }

    public function removeHallMember(HallMember $hallMember): static
    {
        if ($this->hallMembers->removeElement($hallMember)) {
            // set the owning side to null (unless already changed)
            if ($hallMember->getHall() === $this) {
                $hallMember->setHall(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MusicCategory>
     */
    public function getMusicCategory(): Collection
    {
        return $this->music_category;
    }

    public function addMusicCategory(MusicCategory $musicCategory): static
    {
        if (!$this->music_category->contains($musicCategory)) {
            $this->music_category->add($musicCategory);
        }

        return $this;
    }

    public function removeMusicCategory(MusicCategory $musicCategory): static
    {
        $this->music_category->removeElement($musicCategory);

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setHall($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getHall() === $this) {
                $event->setHall(null);
            }
        }

        return $this;
    }

    public function getStructure(): ?string
    {
        return $this->structure;
    }

    public function setStructure(?string $structure): static
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * @return Collection<int, HallInfo>
     */
    public function getHallInfos(): Collection
    {
        return $this->hallInfos;
    }

    public function addHallInfo(HallInfo $hallInfo): static
    {
        if (!$this->hallInfos->contains($hallInfo)) {
            $this->hallInfos->add($hallInfo);
            $hallInfo->setHall($this);
        }

        return $this;
    }

    public function removeHallInfo(HallInfo $hallInfo): static
    {
        if ($this->hallInfos->removeElement($hallInfo)) {
            // set the owning side to null (unless already changed)
            if ($hallInfo->getHall() === $this) {
                $hallInfo->setHall(null);
            }
        }

        return $this;
    }
}
