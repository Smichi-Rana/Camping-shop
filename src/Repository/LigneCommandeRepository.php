<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneCommande>
 *
 * @method LigneCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method LigneCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneCommande[]    findAll()
 * @method LigneCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    // Exemple : Trouver toutes les lignes d'une commande spécifique
    public function findByCommandeId(int $commandeId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.commande = :commandeId')
            ->setParameter('commandeId', $commandeId)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Exemple : Calculer le total d'une commande
    public function getTotalForCommande(int $commandeId): float
    {
        return $this->createQueryBuilder('l')
            ->select('SUM(l.quantite * l.prix) as total')
            ->andWhere('l.commande = :commandeId')
            ->setParameter('commandeId', $commandeId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    // Exemple : Trouver les produits les plus commandés
    public function findMostOrderedProducts(int $limit = 10): array
    {
        return $this->createQueryBuilder('l')
            ->select('p.nom as product_name, SUM(l.quantite) as total_quantity')
            ->join('l.produit', 'p')
            ->groupBy('l.produit')
            ->orderBy('total_quantity', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    // Exemple : Supprimer toutes les lignes d'une commande
    public function removeByCommandeId(int $commandeId): int
    {
        return $this->createQueryBuilder('l')
            ->delete()
            ->where('l.commande = :commandeId')
            ->setParameter('commandeId', $commandeId)
            ->getQuery()
            ->execute();
    }

    // Exemple : Mettre à jour le prix d'un produit dans toutes les lignes
    public function updateProductPrice(int $produitId, float $newPrice): int
    {
        return $this->createQueryBuilder('l')
            ->update()
            ->set('l.prix', ':newPrice')
            ->where('l.produit = :produitId')
            ->setParameter('newPrice', $newPrice)
            ->setParameter('produitId', $produitId)
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return LigneCommande[] Returns an array of LigneCommande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LigneCommande
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
