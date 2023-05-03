<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Plateform::class, orphanRemoval: true)]
    private Collection $plateforms;

    public function __construct()
    {
        $this->plateforms = new ArrayCollection();
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
            $plateform->setCategory($this);
        }

        return $this;
    }

    public function removePlateform(Plateform $plateform): self
    {
        if ($this->plateforms->removeElement($plateform)) {
            // set the owning side to null (unless already changed)
            if ($plateform->getCategory() === $this) {
                $plateform->setCategory(null);
            }
        }

        return $this;
    }
}
