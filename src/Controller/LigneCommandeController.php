<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Form\LigneCommandeType;
use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ligne')]
class LigneCommandeController extends AbstractController
{
    // Liste toutes les lignes de commande
    #[Route('/', name: 'ligne_index', methods: ['GET'])]
    public function index(LigneCommandeRepository $repo): Response
    {
        $lignes = $repo->findAll();
        return $this->render('ligne_commande/index.html.twig', [
            'lignes' => $lignes
        ]);
    }

    // Modifier une ligne de commande
    #[Route('/{id}/edit', name: 'ligne_edit', methods: ['GET','POST'])]
    public function edit(Request $request, LigneCommande $ligne, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LigneCommandeType::class, $ligne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le prix unitaire si le produit a changé
            $product = $ligne->getProduct();
            if ($product) {
                if (method_exists($product, 'getPrix')) {
                    $ligne->setPrixUnitaire($product->getPrix());
                } elseif (method_exists($product, 'getPrice')) {
                    $ligne->setPrixUnitaire($product->getPrice());
                }
            }

            $em->flush();

            // Recalculer le montant total de la commande
            $commande = $ligne->getCommande();
            if ($commande) {
                $total = 0.0;
                foreach ($commande->getLigneCommandes() as $lc) {
                    $total += $lc->getPrixUnitaire() * $lc->getQuantite();
                }
                $commande->setMontantTotal($total);
                $em->flush();
            }

            $this->addFlash('success', 'Ligne mise à jour.');
            return $this->redirectToRoute('commande_show', ['id' => $ligne->getCommande()->getId()]);
        }

        return $this->render('ligne_commande/edit.html.twig', [
            'form' => $form->createView(),
            'ligne' => $ligne
        ]);
    }

    // Supprimer une ligne de commande
    #[Route('/{id}/delete', name: 'ligne_delete', methods: ['POST'])]
    public function delete(Request $request, LigneCommande $ligne, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ligne->getId(), $request->request->get('_token'))) {
            $commande = $ligne->getCommande();
            $em->remove($ligne);
            $em->flush();

            // Recalculer le montant total de la commande
            if ($commande) {
                $total = 0.0;
                foreach ($commande->getLigneCommandes() as $lc) {
                    $total += $lc->getPrixUnitaire() * $lc->getQuantite();
                }
                $commande->setMontantTotal($total);
                $em->flush();
            }

            $this->addFlash('success', 'Ligne supprimée.');
        }

        return $this->redirectToRoute('commande_index');
    }
}
