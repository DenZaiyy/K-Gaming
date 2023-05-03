<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_release = null;

    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'games')]
    private Collection $game_genre;

    #[ORM\ManyToMany(targetEntity: Plateform::class, inversedBy: 'games')]
    private Collection $game_plateform;

    public function __construct()
    {
        $this->game_genre = new ArrayCollection();
        $this->game_plateform = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateRelease(): ?\DateTimeInterface
    {
        return $this->date_release;
    }

    public function setDateRelease(\DateTimeInterface $date_release): self
    {
        $this->date_release = $date_release;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGameGenre(): Collection
    {
        return $this->game_genre;
    }

    public function addGameGenre(Genre $gameGenre): self
    {
        if (!$this->game_genre->contains($gameGenre)) {
            $this->game_genre->add($gameGenre);
        }

        return $this;
    }

    public function removeGameGenre(Genre $gameGenre): self
    {
        $this->game_genre->removeElement($gameGenre);

        return $this;
    }

    /**
     * @return Collection<int, Plateform>
     */
    public function getGamePlateform(): Collection
    {
        return $this->game_plateform;
    }

    public function addGamePlateform(Plateform $gamePlateform): self
    {
        if (!$this->game_plateform->contains($gamePlateform)) {
            $this->game_plateform->add($gamePlateform);
        }

        return $this;
    }

    public function removeGamePlateform(Plateform $gamePlateform): self
    {
        $this->game_plateform->removeElement($gamePlateform);

        return $this;
    }
}
