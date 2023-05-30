<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $label = null;

    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 20)]
    private ?string $cp = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'address', targetEntity: Purchase::class)]
    private Collection $purchase;

    public function __construct()
    {
        $this->purchase = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getLabel();
//            . '[-br]' .
//            $this->getAddress() . '[-br]' .
//            $this->getCp() . ' - ' .
//            $this->getCity();
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchase(): Collection
    {
        return $this->purchase;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchase->contains($purchase)) {
            $this->purchase->add($purchase);
            $purchase->setAddress($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchase->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getAddress() === $this) {
                $purchase->setAddress(null);
            }
        }

        return $this;
    }
}
