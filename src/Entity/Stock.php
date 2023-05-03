<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $game_key = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_stock = null;

    #[ORM\Column]
    private ?bool $is_available = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameKey(): ?string
    {
        return $this->game_key;
    }

    public function setGameKey(string $game_key): self
    {
        $this->game_key = $game_key;

        return $this;
    }

    public function getDateStock(): ?\DateTimeInterface
    {
        return $this->date_stock;
    }

    public function setDateStock(\DateTimeInterface $date_stock): self
    {
        $this->date_stock = $date_stock;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->is_available;
    }

    public function setIsAvailable(bool $is_available): self
    {
        $this->is_available = $is_available;

        return $this;
    }
}
