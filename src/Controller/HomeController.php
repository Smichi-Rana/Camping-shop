<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        CommandeRepository $commandeRepository,
        FactureRepository $factureRepository
    ): Response
    {
        // Récupérer les commandes sans charger les relations problématiques
        $commandes = $commandeRepository->createQueryBuilder('c')
            ->select('c.id, c.dateCommande, c.status, c.montantTotal')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Récupérer les factures sans charger les relations problématiques
        $factures = $factureRepository->createQueryBuilder('f')
            ->select('f.id, f.num_commande, f.total, f.date')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'commandes' => $commandes,
            'factures' => $factures,
        ]);
    }
}
