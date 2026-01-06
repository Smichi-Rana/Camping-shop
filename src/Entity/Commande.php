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
    private ?string $status = null; // <-- sécurisé

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
        $this->status = 'en attente'; // <-- statut par défaut
        $this->ligneCommandes = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
    }

    // ==================== GETTERS / SETTERS ====================

    public function getId(): ?int { return $this->id; }

    public function getDateCommande(): ?\DateTimeInterface { return $this->dateCommande; }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getStatus(): ?string { return $this->status; }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getMontantTotal(): ?string { return $this->montantTotal; }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getAdresseLivraison(): ?string { return $this->adresseLivraison; }

    public function setAdresseLivraison(?string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getCommentaire(): ?string { return $this->commentaire; }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    // ==================== LIGNES ====================

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
        if ($this->ligneCommandes->removeElement($ligneCommande) && $ligneCommande->getCommande() === $this) {
            $ligneCommande->setCommande(null);
        }
        return $this;
    }

    // ==================== FACTURE ====================

    public function getFacture(): ?Facture { return $this->facture; }

    public function setFacture(?Facture $facture): static
    {
        if ($facture === null && $this->facture !== null) {
            $this->facture->setCommande(null);
        }
        if ($facture !== null && $facture->getCommande() !== $this) {
            $facture->setCommande($this);
        }
        $this->facture = $facture;
        return $this;
    }

    // ==================== USER ====================

    public function getUser(): ?User { return $this->user; }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // ==================== MÉTIERS ====================

    public function calculerTotal(): string
    {
        $total = '0.00';
        foreach ($this->ligneCommandes as $ligne) {
            $total = bcadd($total, bcmul($ligne->getPrix(), (string)$ligne->getQuantite(), 2), 2);
        }
        return $this->montantTotal = $total;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateMontantTotal(): void
    {
        $this->calculerTotal();
    }

    public function changerStatut(string $nouveauStatut): static
    {
        $valides = ['en attente','confirmée','en préparation','expédiée','livrée','annulée'];
        if (in_array($nouveauStatut, $valides)) $this->status = $nouveauStatut;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            'Commande #%d - %s - %s€',
            $this->id ?? 0,
            $this->dateCommande?->format('d/m/Y') ?? 'N/A',
            $this->montantTotal
        );
    }
}
