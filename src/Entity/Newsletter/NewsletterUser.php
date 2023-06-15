<?php

namespace App\Entity\Newsletter;

use App\Repository\Newsletter\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class NewsletterUser
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $email = null;

	#[ORM\Column]
	private ?bool $is_verified = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	private ?\DateTimeInterface $created_at = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $token = null;

	#[ORM\Column]
	private ?bool $is_rgpd = null;

	public function __construct()
	{
		$this->created_at = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): static
	{
		$this->email = $email;

		return $this;
	}

	public function isIsVerified(): ?bool
	{
		return $this->is_verified;
	}

	public function setIsVerified(bool $is_verified): static
	{
		$this->is_verified = $is_verified;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->created_at;
	}

	public function setCreatedAt(\DateTimeInterface $created_at): static
	{
		$this->created_at = $created_at;

		return $this;
	}

	public function getToken(): ?string
	{
		return $this->token;
	}

	public function setToken(string $token): static
	{
		$this->token = $token;

		return $this;
	}

	public function isIsRgpd(): ?bool
	{
		return $this->is_rgpd;
	}

	public function setIsRgpd(bool $is_rgpd): static
	{
		$this->is_rgpd = $is_rgpd;

		return $this;
	}
}
