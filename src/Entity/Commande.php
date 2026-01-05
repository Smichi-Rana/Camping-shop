<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'en attente';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantTotal = '0.00';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ligneCommandes;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Reclamation::class, cascade: ['remove'])]
    private Collection $reclamations;

    #[ORM\OneToOne(mappedBy: 'commande', targetEntity: Facture::class, cascade: ['persist', 'remove'])]
    private ?Facture $facture = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->dateCommande = new \DateTime();
        $this->ligneCommandes = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
    }

    // ==================== GETTERS / SETTERS ====================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(?string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    // ==================== RELATIONS - LIGNE COMMANDES ====================

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

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

    // ==================== RELATIONS - RECLAMATIONS ====================

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setCommande($this);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            if ($reclamation->getCommande() === $this) {
                $reclamation->setCommande(null);
            }
        }
        return $this;
    }

    // ==================== RELATIONS - FACTURE ====================

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): static
    {
        // Unset the owning side of the relation if necessary
        if ($facture === null && $this->facture !== null) {
            $this->facture->setCommande(null);
        }

        // Set the owning side of the relation if necessary
        if ($facture !== null && $facture->getCommande() !== $this) {
            $facture->setCommande($this);
        }

        $this->facture = $facture;
        return $this;
    }

    // ==================== RELATIONS - USER ====================

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // ==================== MÉTHODES UTILITAIRES ====================

    /**
     * Calcule le montant total de la commande en fonction des lignes de commande
     */
    public function calculerTotal(): string
    {
        $total = '0.00';
        foreach ($this->ligneCommandes as $ligne) {
            $sousTotal = bcmul($ligne->getPrix(), (string)$ligne->getQuantite(), 2);
            $total = bcadd($total, $sousTotal, 2);
        }
        $this->montantTotal = $total;
        return $total;
    }

    /**
     * Met à jour automatiquement le montant total avant la persistance
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateMontantTotal(): void
    {
        $this->calculerTotal();
    }

    /**
     * Retourne le nombre total d'articles dans la commande
     */
    public function getNombreArticles(): int
    {
        $total = 0;
        foreach ($this->ligneCommandes as $ligne) {
            $total += $ligne->getQuantite();
        }
        return $total;
    }

    /**
     * Vérifie si la commande est vide
     */
    public function isEmpty(): bool
    {
        return $this->ligneCommandes->isEmpty();
    }

    /**
     * Vérifie si la commande a une facture
     */
    public function hasFacture(): bool
    {
        return $this->facture !== null;
    }

    /**
     * Vérifie si la commande a des réclamations
     */
    public function hasReclamations(): bool
    {
        return !$this->reclamations->isEmpty();
    }

    /**
     * Retourne le statut avec un badge de couleur pour l'affichage
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'en attente' => 'warning',
            'validée', 'confirmée' => 'success',
            'en préparation' => 'info',
            'expédiée', 'livrée' => 'primary',
            'annulée' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Change le statut de la commande
     */
    public function changerStatut(string $nouveauStatut): static
    {
        $statutsValides = [
            'en attente',
            'confirmée',
            'en préparation',
            'expédiée',
            'livrée',
            'annulée'
        ];

        if (in_array($nouveauStatut, $statutsValides)) {
            $this->status = $nouveauStatut;
        }

        return $this;
    }

    /**
     * Retourne une représentation textuelle de la commande
     */
    public function __toString(): string
    {
        return sprintf(
            'Commande #%d - %s - %s€',
            $this->id ?? 0,
            $this->dateCommande ? $this->dateCommande->format('d/m/Y') : 'N/A',
            $this->montantTotal
        );
    }

    /**
     * Génère un numéro de commande formaté
     */
    public function getNumeroCommande(): string
    {
        return sprintf(
            'CMD-%s-%05d',
            $this->dateCommande ? $this->dateCommande->format('Ymd') : date('Ymd'),
            $this->id ?? 0
        );
    }
}
