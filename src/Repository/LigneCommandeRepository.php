<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    /**
     * Retourne toutes les lignes d'une commande
     */
    public function findByCommande(Commande $commande): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.commande = :commande')
            ->setParameter('commande', $commande)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcul total d'une ligne : quantite × prixUnitaire
     */
    public function calculTotalLigne(LigneCommande $ligne): float
    {
        return $ligne->getQuantite() * $ligne->getPrixUnitaire();
    }

    /**
     * Calcule le total de toutes les lignes d'une commande
     */
    public function totalCommande(Commande $commande): float
    {
        $lignes = $this->findByCommande($commande);
        $total = 0;

        foreach ($lignes as $ligne) {
            $total += $ligne->getQuantite() * $ligne->getPrixUnitaire();
        }

        return $total;
    }
}
