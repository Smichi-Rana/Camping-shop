<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Facture;
use App\Form\CommandeType;
use App\Form\LigneCommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    // Liste toutes les commandes
    #[Route('/', name: 'commande_index', methods: ['GET'])]
    public function index(CommandeRepository $repo): Response
    {
        $commandes = $repo->findAll();
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes
        ]);
    }

    // Créer une nouvelle commande
    #[Route('/new', name: 'commande_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $commande = new Commande();
        $commande->setDateCommande(new \DateTimeImmutable());
        $commande->setStatus('en_attente');
        $commande->setMontantTotal(0.0);

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commande);
            $em->flush();
            $this->addFlash('success', 'Commande créée.');
            return $this->redirectToRoute('commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Affiche une commande et ses lignes + formulaire pour ajouter une ligne
    #[Route('/{id}', name: 'commande_show', methods: ['GET','POST'])]
    public function show(Commande $commande, Request $request, EntityManagerInterface $em): Response
    {
        $ligne = new LigneCommande();
        $ligne->setCommande($commande);

        $ligneForm = $this->createForm(LigneCommandeType::class, $ligne);
        $ligneForm->handleRequest($request);

        if ($ligneForm->isSubmitted() && $ligneForm->isValid()) {
            $product = $ligne->getProduct();
            if ($product === null) {
                $this->addFlash('danger', 'Choisissez un produit.');
                return $this->redirectToRoute('commande_show', ['id' => $commande->getId()]);
            }

            // Récupère le prix du produit
            $prix = method_exists($product, 'getPrix') ? $product->getPrix() :
                (method_exists($product, 'getPrice') ? $product->getPrice() : 0.0);
            $ligne->setPrixUnitaire((float)$prix);

            $em->persist($ligne);

            // Recalcul du montant total de la commande
            $total = 0.0;
            foreach ($commande->getLigneCommandes() as $existing) {
                $total += ($existing->getPrixUnitaire() * $existing->getQuantite());
            }
            $total += ($ligne->getPrixUnitaire() * $ligne->getQuantite());
            $commande->setMontantTotal($total);

            $em->flush();
            $this->addFlash('success', 'Ligne ajoutée.');
            return $this->redirectToRoute('commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
            'ligne_form' => $ligneForm->createView(),
            'montantTotal' => $commande->getMontantTotal()
        ]);
    }

    // Modifier une commande
    #[Route('/{id}/edit', name: 'commande_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Commande mise à jour.');
            return $this->redirectToRoute('commande_index');
        }

        return $this->render('commande/edit.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande
        ]);
    }

    // Supprimer une commande
    #[Route('/{id}/delete', name: 'commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $em->remove($commande);
            $em->flush();
            $this->addFlash('success', 'Commande supprimée.');
        }
        return $this->redirectToRoute('commande_index');
    }

    // Changer le status de la commande et générer facture automatiquement si nécessaire
    #[Route('/{id}/status/{status}', name: 'commande_change_status', methods: ['POST','GET'])]
    public function changeStatus(Commande $commande, string $status, EntityManagerInterface $em): Response
    {
        $commande->setStatus($status);

        if (in_array($status, ['valide','payee','payée']) && $commande->getMontantTotal() > 0) {
            if (!$commande->getFacture()) {
                $facture = new Facture();
                $facture->setMontant($commande->getMontantTotal());
                $facture->setDateFacture(new \DateTimeImmutable());
                $facture->setUser($commande->getUser());
                $facture->setCommande($commande);

                $em->persist($facture);
            }
        }

        $em->flush();
        $this->addFlash('success', 'Statut modifié.');
        return $this->redirectToRoute('commande_show', ['id' => $commande->getId()]);
    }
}
