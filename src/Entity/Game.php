<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTimeInterface;
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

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $date_release = null;

    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: "games")]
    private Collection $genres;

    #[ORM\ManyToMany(targetEntity: Plateform::class, inversedBy: "games")]
    private Collection $plateforms;

    #[ORM\OneToMany(mappedBy: "game", targetEntity: Stock::class)]
    private Collection $stocks;

    #[ORM\OneToMany(mappedBy: "game", targetEntity: Rating::class)]
    private Collection $ratings;

    #[ORM\Column]
    private ?bool $is_promotion = null;

    #[ORM\Column(nullable: true)]
    private ?float $promo_percent = null;

    #[ORM\Column(nullable: true)]
    private ?float $old_price = null;

    #[ORM\Column]
    private ?bool $is_sellable = true;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->plateforms = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->ratings = new ArrayCollection();
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

    public function getDateRelease(): ?DateTimeInterface
    {
        return $this->date_release;
    }

    public function setDateRelease(DateTimeInterface $date_release): self
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

    public function addGenre(Genre $genre): self
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setGame($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getGame() === $this) {
                $rating->setGame(null);
            }
        }

        return $this;
    }

    public function isIsPromotion(): ?bool
    {
        return $this->is_promotion;
    }

    public function setIsPromotion(bool $is_promotion): static
    {
        $this->is_promotion = $is_promotion;

        return $this;
    }

    public function getPromoPercent(): ?float
    {
        return $this->promo_percent;
    }

    public function setPromoPercent(?float $promo_percent): static
    {
        $this->promo_percent = $promo_percent;

        return $this;
    }

    public function getOldPrice(): ?float
    {
        return $this->old_price;
    }

    public function setOldPrice(?float $old_price): static
    {
        $this->old_price = $old_price;

        return $this;
    }

    public function isIsSellable(): ?bool
    {
        return $this->is_sellable;
    }

    public function setIsSellable(bool $is_sellable): static
    {
        $this->is_sellable = $is_sellable;

        return $this;
    }
}
