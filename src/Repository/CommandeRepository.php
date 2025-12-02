<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    /**
     * Retourne les commandes par statut (ex: en_attente, validee, livree)
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', $status)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les commandes dans un intervalle de dates
     */
    public function findByDateRange(\DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.dateCommande BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le montant total d'une commande
     */
    public function getMontantTotal(Commande $commande): float
    {
        $total = 0;

        foreach ($commande->getLigneCommandes() as $ligne) {
            $total += $ligne->getQuantite() * $ligne->getPrixUnitaire();
        }

        return $total;
    }
}
