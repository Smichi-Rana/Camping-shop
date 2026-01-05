<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Commande;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/facture')]
class FactureController extends AbstractController
{
    // Liste pour ADMIN
    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/index.html.twig', [
            'factures' => $factureRepository->findAll(),
        ]);
    }

    // Mes factures pour CLIENT
    #[Route('/mes-factures', name: 'app_mes_factures', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mesFactures(FactureRepository $factureRepository): Response
    {
        $user = $this->getUser();

        return $this->render('facture/mes_factures.html.twig', [
            'factures' => $factureRepository->findBy(['user' => $user], ['dateFacture' => 'DESC']),
        ]);
    }

    // Créer une facture depuis une commande (ADMIN)
    #[Route('/generer/{commandeId}', name: 'app_facture_generer', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function generer(int $commandeId, EntityManagerInterface $entityManager, Request $request): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($commandeId);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Créer la facture automatiquement
        $facture = new Facture();
        $facture->setCommande($commande);
        $facture->setUser($commande->getUser());
        $facture->setTotal($commande->getMontantTotal());
        $facture->setDateFacture(new \DateTime());
        $facture->setDateEcheance((new \DateTime())->modify('+30 days'));
        $facture->setStatut('en attente');
        $facture->setNumCommande('FAC-' . date('Y') . '-' . str_pad($commandeId, 6, '0', STR_PAD_LEFT));

        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facture);
            $entityManager->flush();

            $this->addFlash('success', 'Facture générée avec succès !');
            return $this->redirectToRoute('app_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render('facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    // Voir détails facture
    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Facture $facture): Response
    {
        // Vérifier que l'utilisateur peut voir cette facture
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') && $facture->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette facture.');
        }

        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    // Marquer comme payée (ADMIN)
    #[Route('/{id}/payer', name: 'app_facture_payer', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function payer(Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $facture->setStatut('payée');
        $entityManager->flush();

        $this->addFlash('success', 'Facture marquée comme payée !');
        return $this->redirectToRoute('app_facture_index');
    }
}
