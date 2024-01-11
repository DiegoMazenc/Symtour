<?php

namespace App\Entity;

use App\Repository\MusicCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicCategoryRepository::class)]
class MusicCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category = null;

    #[ORM\ManyToMany(targetEntity: Hall::class, mappedBy: 'music_category')]
    private Collection $halls;

    public function __construct()
    {
        $this->halls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Hall>
     */
    public function getHalls(): Collection
    {
        return $this->halls;
    }

    public function addHall(Hall $hall): static
    {
        if (!$this->halls->contains($hall)) {
            $this->halls->add($hall);
            $hall->addMusicCategory($this);
        }

        return $this;
    }

    public function removeHall(Hall $hall): static
    {
        if ($this->halls->removeElement($hall)) {
            $hall->removeMusicCategory($this);
        }

        return $this;
    }
}
