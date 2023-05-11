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

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_release = null;

    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'games')]
    private Collection $genres;

    #[ORM\ManyToMany(targetEntity: Plateform::class, inversedBy: 'games')]
    private Collection $plateforms;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Stock::class)]
    private Collection $stocks;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->plateforms = new ArrayCollection();
        $this->stocks = new ArrayCollection();
    }

	public function __toString(): string
	{
		return $this->label;
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
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): sself
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    /**
     * @return Collection<int, Plateform>
     */
    public function getPlateforms(): Collection
    {
        return $this->plateforms;
    }

    public function addPlateform(Plateform $plateform): self
    {
        if (!$this->plateforms->contains($plateform)) {
            $this->plateforms->add($plateform);
        }

        return $this;
    }

    public function removePlateform(Plateform $plateform): self
    {
        $this->plateforms->removeElement($plateform);

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setGame($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getGame() === $this) {
                $stock->setGame(null);
            }
        }

        return $this;
    }
}
