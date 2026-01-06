<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('p.dateTransaction', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function calculerTotalParStatut(string $statut): float
    {
        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.montant) as total')
            ->andWhere('p.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float)$result : 0;
    }

    public function findPaiementsRecents(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.dateTransaction', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByMethodePaiement(string $methode): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.methodePaiement = :methode')
            ->setParameter('methode', $methode)
            ->orderBy('p.dateTransaction', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTime $dateDebut, \DateTime $dateFin): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateTransaction BETWEEN :debut AND :fin')
            ->setParameter('debut', $dateDebut)
            ->setParameter('fin', $dateFin)
            ->orderBy('p.dateTransaction', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
