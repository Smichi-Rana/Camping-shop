<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?float $prixUnitaire = null;

    // La commande à laquelle cette ligne appartient
    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    private ?Commande $commande = null;

    // Le produit associé à cette ligne
    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    private ?Product $product = null;

    // -------- Getters & Setters --------
    public function getId(): ?int { return $this->id; }
    public function getQuantite(): ?int { return $this->quantite; }
    public function setQuantite(int $quantite): static { $this->quantite = $quantite; return $this; }

    public function getPrixUnitaire(): ?float { return $this->prixUnitaire; }
    public function setPrixUnitaire(float $prixUnitaire): static { $this->prixUnitaire = $prixUnitaire; return $this; }

    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }

    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(?Product $product): static { $this->product = $product; return $this; }
}
