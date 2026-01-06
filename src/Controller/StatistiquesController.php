<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'app_statistiques')]
    #[IsGranted('ROLE_USER')]
    public function index(CommandeRepository $commandeRepository, FactureRepository $factureRepository): Response
    {
        $user = $this->getUser();

        // Statistiques du client
        if (!$this->isGranted('ROLE_ADMIN')) {
            $commandes = $commandeRepository->findBy(['user' => $user]);
            $factures = $factureRepository->findBy(['user' => $user]);

            $stats = [
                'total_commandes' => count($commandes),
                'commandes_en_attente' => count(array_filter($commandes, fn($c) => $c->getStatut() === 'en attente')),
                'commandes_validees' => count(array_filter($commandes, fn($c) => $c->getStatut() === 'validée')),
                'total_depense' => array_sum(array_map(fn($c) => $c->getMontantTotal(), $commandes)),
                'factures_payees' => count(array_filter($factures, fn($f) => $f->getStatut() === 'payée')),
                'factures_en_attente' => count(array_filter($factures, fn($f) => $f->getStatut() === 'en attente')),
            ];

            return $this->render('statistiques/user.html.twig', [
                'stats' => $stats,
            ]);
        }

        // Statistiques admin
        $commandes = $commandeRepository->findAll();
        $factures = $factureRepository->findAll();

        $stats = [
            'total_commandes' => count($commandes),
            'commandes_en_attente' => count(array_filter($commandes, fn($c) => $c->getStatut() === 'en attente')),
            'commandes_validees' => count(array_filter($commandes, fn($c) => $c->getStatut() === 'validée')),
            'chiffre_affaires' => array_sum(array_map(fn($f) => $f->getTotal(), $factures)),
            'factures_payees' => count(array_filter($factures, fn($f) => $f->getStatut() === 'payée')),
            'factures_en_attente' => count(array_filter($factures, fn($f) => $f->getStatut() === 'en attente')),
        ];

        return $this->render('statistiques/admin.html.twig', [
            'stats' => $stats,
        ]);
    }
}
