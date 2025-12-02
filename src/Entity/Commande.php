<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Date à laquelle la commande a été passée
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateCommande = null;

    // Statut de la commande ("En cours", "Payée", "Annulée", etc.)
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    // Total de la commande (calculé depuis les lignes de commande)
    #[ORM\Column]
    private ?float $montantTotal = null;

    // L'utilisateur qui a passé la commande
    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $user = null;

    /**
     * @var Collection<int, LigneCommande>
     * Lignes de commande associées
     */
    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'commande', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ligneCommandes;

    /**
     * Relation OneToOne avec Facture
     */
    #[ORM\OneToOne(mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private ?Facture $facture = null;

    public function __construct()
    {
        $this->dateCommande = new \DateTimeImmutable();
        $this->ligneCommandes = new ArrayCollection();
    }

    // -------- Getters & Setters --------
    public function getId(): ?int { return $this->id; }
    public function getDateCommande(): ?\DateTimeImmutable { return $this->dateCommande; }
    public function setDateCommande(\DateTimeImmutable $dateCommande): static { $this->dateCommande = $dateCommande; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getMontantTotal(): ?float { return $this->montantTotal; }
    public function setMontantTotal(float $montantTotal): static { $this->montantTotal = $montantTotal; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getLigneCommandes(): Collection { return $this->ligneCommandes; }
    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }
        return $this;
    }
    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }
        return $this;
    }

    public function getFacture(): ?Facture { return $this->facture; }
    public function setFacture(Facture $facture): static
    {
        // Assure la cohérence bidirectionnelle
        if ($facture->getCommande() !== $this) {
            $facture->setCommande($this);
        }
        $this->facture = $facture;
        return $this;
    }
}
