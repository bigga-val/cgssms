<?php

namespace App\Entity;

use App\Repository\CompteSMSRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteSMSRepository::class)]
class CompteSMS
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?int $smsTotal = null;

    #[ORM\Column(nullable: true)]
    private ?int $smsUsed = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSmsTotal(): ?int
    {
        return $this->smsTotal;
    }

    public function setSmsTotal(?int $smsTotal): static
    {
        $this->smsTotal = $smsTotal;

        return $this;
    }

    public function getSmsUsed(): ?int
    {
        return $this->smsUsed;
    }

    public function setSmsUsed(?int $smsUsed): static
    {
        $this->smsUsed = $smsUsed;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }
}
