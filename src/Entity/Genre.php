<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: "genres")]
    private Collection $games;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $image = null;

    public function __construct ()
    {
        $this->games = new ArrayCollection();
    }

    public function __toString (): string
    {
        return $this->label;
    }

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getLabel (): ?string
    {
        return $this->label;
    }

    public function setLabel (string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames (): Collection
    {
        return $this->games;
    }

    public function addGame (Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->addGenre($this);
        }

        return $this;
    }

    public function removeGame (Game $game): self
    {
        if ($this->games->removeElement($game)) {
            $game->removeGenre($this);
        }

        return $this;
    }

    public function getSlug (): ?string
    {
        return $this->slug;
    }

    public function setSlug (string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage (): ?string
    {
        return $this->image;
    }

    public function setImage (string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
