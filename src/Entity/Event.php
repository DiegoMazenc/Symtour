<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Hall $hall = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(nullable: true)]
    private ?int $status = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: BandEvent::class)]
    private Collection $bandEvents;


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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, BandEvent>
     */

    /**
     * @return Collection<int, BandEvent>
     */
    public function getBandEvents(): Collection
    {
        return $this->bandEvents;
    }

    public function addBandEvent(BandEvent $bandEvent): static
    {
        if (!$this->bandEvents->contains($bandEvent)) {
            $this->bandEvents->add($bandEvent);
            $bandEvent->setEvent($this);
        }

        return $this;
    }

    public function removeBandEvent(BandEvent $bandEvent): static
    {
        if ($this->bandEvents->removeElement($bandEvent)) {
            // set the owning side to null (unless already changed)
            if ($bandEvent->getEvent() === $this) {
                $bandEvent->setEvent(null);
            }
        }

        return $this;
    }

}
