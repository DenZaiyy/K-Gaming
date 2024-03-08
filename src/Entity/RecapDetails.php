<?php

namespace App\Entity;

use App\Repository\RecapDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecapDetailsRepository::class)]
class RecapDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: "recapDetails")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Purchase $orderProduct = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $gameLabel = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $totalRecap = null;

    #[ORM\Column(length: 255)]
    private ?string $platformLabel = null;

    #[ORM\Column]
    private ?int $game_id = null;

    #[ORM\Column]
    private ?int $platform_id = null;

    #[ORM\Column(length: 255)]
    private ?string $game_slug = null;

    #[ORM\Column(length: 255)]
    private ?string $platform_slug = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getOrderProduct (): ?Purchase
    {
        return $this->orderProduct;
    }

    public function setOrderProduct (?Purchase $orderProduct): self
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }

    public function getTotalRecap (): ?string
    {
        return $this->totalRecap;
    }

    public function setTotalRecap (string $totalRecap): self
    {
        $this->totalRecap = $totalRecap;

        return $this;
    }

    public function getGameId (): ?int
    {
        return $this->game_id;
    }

    public function setGameId (int $game_id): self
    {
        $this->game_id = $game_id;

        return $this;
    }

    public function getPlatformId (): ?int
    {
        return $this->platform_id;
    }

    public function setPlatformId (int $platform_id): self
    {
        $this->platform_id = $platform_id;

        return $this;
    }

    public function getGameSlug (): ?string
    {
        return $this->game_slug;
    }

    public function setGameSlug (string $game_slug): static
    {
        $this->game_slug = $game_slug;

        return $this;
    }

    public function getPlatformSlug (): ?string
    {
        return $this->platform_slug;
    }

    public function setPlatformSlug (string $platform_slug): static
    {
        $this->platform_slug = $platform_slug;

        return $this;
    }

    public function getCategory (): ?Category
    {
        return $this->category;
    }

    public function setCategory (?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function __toString (): string
    {
        return "Jeux : " . $this->getGameLabel() . " | Plateforme : " . $this->getPlatformLabel(
          ) . " | Quantité : " . $this->getQuantity() . " | Prix unitaire : " . $this->getPrice();
    }

    public function getGameLabel (): ?string
    {
        return $this->gameLabel;
    }

    public function setGameLabel (string $gameLabel): self
    {
        $this->gameLabel = $gameLabel;

        return $this;
    }

    public function getPlatformLabel (): ?string
    {
        return $this->platformLabel;
    }

    public function setPlatformLabel (string $platformLabel): self
    {
        $this->platformLabel = $platformLabel;

        return $this;
    }

    public function getQuantity (): ?int
    {
        return $this->quantity;
    }

    public function setQuantity (int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice (): ?float
    {
        return $this->price;
    }

    public function setPrice (float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
