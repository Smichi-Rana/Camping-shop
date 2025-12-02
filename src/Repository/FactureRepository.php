<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    /**
     * Trouver une facture liée à une commande
     */
    public function findByCommande(int $commandeId): ?Facture
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.commande = :cmd')
            ->setParameter('cmd', $commandeId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retourne les factures dans un intervalle de dates
     */
    public function findFacturesByDate(\DateTime $debut, \DateTime $fin): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.dateFacture BETWEEN :d1 AND :d2')
            ->setParameter('d1', $debut)
            ->setParameter('d2', $fin)
            ->orderBy('f.dateFacture', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcul du total de la facture
     * (si tu veux générer automatiquement le montant)
     */
    public function calculMontantFacture(Facture $facture): float
    {
        $commande = $facture->getCommande();
        $total = 0;

        foreach ($commande->getLigneCommandes() as $ligne) {
            $total += $ligne->getQuantite() * $ligne->getPrixUnitaire();
        }

        return $total;
    }
}
