<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\OneToMany(mappedBy: 'purchase', targetEntity: Stock::class)]
    private Collection $stock;

    #[ORM\ManyToOne(inversedBy: 'purchase')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'purchase')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\Column]
    private ?bool $isPaid = null;

    #[ORM\Column(length: 255)]
    private ?string $method = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paypalOrderId = null;

    public function __construct()
    {
        $this->stock = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->address->getFirstname() . ' ' . $this->address->getLastname();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStock(): Collection
    {
        return $this->stock;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stock->contains($stock)) {
            $this->stock->add($stock);
            $stock->setPurchase($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stock->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getPurchase() === $this) {
                $stock->setPurchase(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): self
    {
        $this->stripeSessionId = $stripeSessionId;

        return $this;
    }

    public function getPaypalOrderId(): ?string
    {
        return $this->paypalOrderId;
    }

    public function setPaypalOrderId(?string $paypalOrderId): self
    {
        $this->paypalOrderId = $paypalOrderId;

        return $this;
    }
}
