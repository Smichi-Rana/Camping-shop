<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numCommande = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = '0.00';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFacture = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEcheance = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = 'en attente';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\OneToOne(inversedBy: 'facture', targetEntity: Commande::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->dateFacture = new \DateTime();
    }

    // ==================== GETTERS / SETTERS ====================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCommande(): ?string
    {
        return $this->numCommande;
    }

    public function setNumCommande(string $numCommande): static
    {
        $this->numCommande = $numCommande;
        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getDateFacture(): ?\DateTimeInterface
    {
        return $this->dateFacture;
    }

    public function setDateFacture(?\DateTimeInterface $dateFacture): static
    {
        $this->dateFacture = $dateFacture;
        return $this;
    }

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(?\DateTimeInterface $dateEcheance): static
    {
        $this->dateEcheance = $dateEcheance;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
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

    // ==================== RELATIONS ====================

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
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

    // ==================== MÉTHODES UTILITAIRES ====================

    /**
     * Génère automatiquement un numéro de facture
     */
    #[ORM\PrePersist]
    public function generateNumCommande(): void
    {
        if (!$this->numCommande) {
            $this->numCommande = 'FACT-' . date('Ymd') . '-' . uniqid();
        }
    }

    /**
     * Synchronise le total avec la commande associée
     */
    public function syncTotalFromCommande(): void
    {
        if ($this->commande) {
            $this->total = $this->commande->getMontantTotal();
        }
    }

    /**
     * Vérifie si la facture est en retard
     */
    public function isEnRetard(): bool
    {
        if ($this->dateEcheance && $this->statut !== 'payée') {
            return $this->dateEcheance < new \DateTime();
        }
        return false;
    }

    /**
     * Retourne le badge CSS selon le statut
     */
    public function getStatutBadgeClass(): string
    {
        return match($this->statut) {
            'en attente' => 'warning',
            'payée' => 'success',
            'annulée' => 'danger',
            'remboursée' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Calcule le nombre de jours avant échéance
     */
    public function getJoursAvantEcheance(): ?int
    {
        if (!$this->dateEcheance) {
            return null;
        }
        $now = new \DateTime();
        $interval = $now->diff($this->dateEcheance);
        return $interval->invert ? -$interval->days : $interval->days;
    }

    public function __toString(): string
    {
        return $this->numCommande ?? 'Facture #' . ($this->id ?? 'nouvelle');
    }
}
