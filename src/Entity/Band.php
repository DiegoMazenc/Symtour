<?php

namespace App\Entity;

use App\Repository\BandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BandRepository::class)]
class Band
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $define_style = null;


    #[ORM\OneToMany(mappedBy: 'band', targetEntity: BandMember::class)]
    private Collection $bandMembers;

    #[ORM\ManyToOne]
    private ?MusicCategory $music_category = null;

    #[ORM\OneToMany(mappedBy: 'band', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'band', targetEntity: BandInfo::class)]
    private Collection $bandInfos;

    #[ORM\OneToOne(mappedBy: 'bandId', cascade: ['persist', 'remove'])]
    private ?BandInfo $bandInfo = null;

    public function __construct()
    {
        $this->bandMembers = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->bandInfos = new ArrayCollection();
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

    public function getDefineStyle(): ?string
    {
        return $this->define_style;
    }

    public function setDefineStyle(?string $define_style): static
    {
        $this->define_style = $define_style;

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
            $bandMember->setBand($this);
        }

        return $this;
    }

    public function removeBandMember(BandMember $bandMember): static
    {
        if ($this->bandMembers->removeElement($bandMember)) {
            // set the owning side to null (unless already changed)
            if ($bandMember->getBand() === $this) {
                $bandMember->setBand(null);
            }
        }

        return $this;
    }

    public function getMusicCategory(): ?MusicCategory
    {
        return $this->music_category;
    }

    public function setMusicCategory(?MusicCategory $music_category): static
    {
        $this->music_category = $music_category;

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
            $event->setBand($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getBand() === $this) {
                $event->setBand(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BandInfo>
     */
    public function getBandInfos(): Collection
    {
        return $this->bandInfos;
    }

    public function getBandInfo(): ?BandInfo
    {
        return $this->bandInfo;
    }

    public function setBandInfo(?BandInfo $bandInfo): static
    {
        // unset the owning side of the relation if necessary
        if ($bandInfo === null && $this->bandInfo !== null) {
            $this->bandInfo->setBandId(null);
        }

        // set the owning side of the relation if necessary
        if ($bandInfo !== null && $bandInfo->getBandId() !== $this) {
            $bandInfo->setBandId($this);
        }

        $this->bandInfo = $bandInfo;

        return $this;
    }

   
}
