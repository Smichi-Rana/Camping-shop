<?php

namespace App\Controller;

use App\Repository\PaiementRepository;
use App\Repository\AvisClientRepository;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(
        Request $request,
        PaiementRepository $paiementRepo,
        AvisClientRepository $avisRepo,
        ReclamationRepository $reclamationRepo)
    {
        $paiements = $paiementRepo->findAll();
        $avis = $avisRepo->findAll();
        $reclamations = $reclamationRepo->findAll();

        // ✅ Panier depuis cookie
        $cart = [];
        if ($request->cookies->has('cart')) {
            $cart = json_decode($request->cookies->get('cart'), true) ?? [];
        }

        return $this->render('dashboard/index.html.twig', [
            'paiements' => $paiementRepo->findAll(),
            'avis' => $avisRepo->findAll(),
            'reclamations' => $reclamationRepo->findAll(),
            'cart' => $cart,
        ]);
    }
}
