<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateFacture = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?user $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFacture(): ?\DateTimeImmutable
    {
        return $this->dateFacture;
    }

    public function setDateFacture(\DateTimeImmutable $dateFacture): static
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCommande(): ?commande
    {
        return $this->commande;
    }

    public function setCommande(?commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }
}
