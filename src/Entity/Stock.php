<?php

namespace App\Entity;

use App\Repository\StockRepository;
use DateTimeInterface;
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
    private ?string $license_key = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date_availability = null;

    #[ORM\Column]
    private ?bool $is_available = null;

    #[ORM\ManyToOne(inversedBy: "stocks")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Game $game = null;

    #[ORM\ManyToOne(inversedBy: "stock")]
    private ?Purchase $purchase = null;

    #[ORM\ManyToOne(inversedBy: "stocks")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Plateform $plateform = null;

    public function __toString (): string
    {
        return $this->license_key;
    }

    public function getId (): ?int
    {
        return $this->id;
    }

    public function getLicenseKey (): ?string
    {
        return $this->license_key;
    }

    public function setLicenseKey (string $license_key): self
    {
        $this->license_key = $license_key;

        return $this;
    }

    public function getDateAvailability (): ?DateTimeInterface
    {
        return $this->date_availability;
    }

    public function setDateAvailability (DateTimeInterface $date_availability): self
    {
        $this->date_availability = $date_availability;

        return $this;
    }

    public function isIsAvailable (): ?bool
    {
        return $this->is_available;
    }

    public function setIsAvailable (bool $is_available): self
    {
        $this->is_available = $is_available;

        return $this;
    }

    public function getGame (): ?Game
    {
        return $this->game;
    }

    public function setGame (?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getPurchase (): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase (?Purchase $purchase): self
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getPlateform (): ?Plateform
    {
        return $this->plateform;
    }

    public function setPlateform (?Plateform $plateform): self
    {
        $this->plateform = $plateform;

        return $this;
    }
}
