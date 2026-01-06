<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function findAvisValides(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.estValide = :valide')
            ->setParameter('valide', true)
            ->orderBy('a.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAvisEnAttente(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.estValide = :valide')
            ->setParameter('valide', false)
            ->orderBy('a.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function calculerMoyenneNotes(): float
    {
        $result = $this->createQueryBuilder('a')
            ->select('AVG(a.note) as moyenne')
            ->andWhere('a.estValide = :valide')
            ->setParameter('valide', true)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? round($result, 2) : 0;
    }

    public function findByNote(int $note): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.note = :note')
            ->andWhere('a.estValide = :valide')
            ->setParameter('note', $note)
            ->setParameter('valide', true)
            ->orderBy('a.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
