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

    // Date de la facture
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateFacture = null;

    // Montant total (calculé depuis la commande)
    #[ORM\Column]
    private ?float $montant = null;

    // Relation OneToOne vers la commande
    #[ORM\OneToOne(inversedBy: 'facture', cascade: ['persist', 'remove'])]
    private ?Commande $commande = null;

    // L'utilisateur qui reçoit la facture
    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?User $user = null;

    public function __construct()
    {
        $this->dateFacture = new \DateTimeImmutable();
    }

    // -------- Getters & Setters --------
    public function getId(): ?int { return $this->id; }
    public function getDateFacture(): ?\DateTimeImmutable { return $this->dateFacture; }
    public function setDateFacture(\DateTimeImmutable $dateFacture): static { $this->dateFacture = $dateFacture; return $this; }

    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(float $montant): static { $this->montant = $montant; return $this; }

    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static
    {
        // Assure la cohérence bidirectionnelle
        if ($commande->getFacture() !== $this) {
            $commande->setFacture($this);
        }
        $this->commande = $commande;
        return $this;
    }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
}
