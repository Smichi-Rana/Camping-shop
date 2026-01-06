<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Form\PaiementFormType;
use App\Repository\PaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/paiement')]
class PaiementController extends AbstractController
{
    #[Route('/', name: 'app_paiement_index', methods: ['GET'])]
    public function index(PaiementRepository $paiementRepository): Response
    {
        return $this->render('paiement/index.html.twig', [
            'paiements' => $paiementRepository->findBy([], ['dateTransaction' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_paiement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paiement = new Paiement();
        $form = $this->createForm(PaiementFormType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paiement);
            $entityManager->flush();

            $this->addFlash('success', 'Paiement enregistré avec succès! Référence: ' . $paiement->getReference());

            return $this->redirectToRoute('app_paiement_show', ['id' => $paiement->getId()]);
        }

        return $this->render('paiement/new.html.twig', [
            'paiement' => $paiement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paiement_show', methods: ['GET'])]
    public function show(Paiement $paiement): Response
    {
        return $this->render('paiement/show.html.twig', [
            'paiement' => $paiement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_paiement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Paiement $paiement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaiementFormType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Paiement mis à jour avec succès!');

            return $this->redirectToRoute('app_paiement_index');
        }

        return $this->render('paiement/edit.html.twig', [
            'paiement' => $paiement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paiement_delete', methods: ['POST'])]
    public function delete(Request $request, Paiement $paiement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paiement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($paiement);
            $entityManager->flush();

            $this->addFlash('success', 'Paiement supprimé avec succès!');
        }

        return $this->redirectToRoute('app_paiement_index');
    }

    #[Route('/{id}/valider', name: 'app_paiement_valider', methods: ['POST'])]
    public function valider(Request $request, Paiement $paiement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('valider'.$paiement->getId(), $request->request->get('_token'))) {
            $paiement->setStatut('valide');
            $entityManager->flush();

            $this->addFlash('success', 'Paiement validé avec succès!');
        }

        return $this->redirectToRoute('app_paiement_show', ['id' => $paiement->getId()]);
    }

    #[Route('/{id}/annuler', name: 'app_paiement_annuler', methods: ['POST'])]
    public function annuler(Request $request, Paiement $paiement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('annuler'.$paiement->getId(), $request->request->get('_token'))) {
            $paiement->setStatut('annule');
            $entityManager->flush();

            $this->addFlash('warning', 'Paiement annulé!');
        }

        return $this->redirectToRoute('app_paiement_show', ['id' => $paiement->getId()]);
    }

    #[Route('/reference/{reference}', name: 'app_paiement_by_reference', methods: ['GET'])]
    public function findByReference(string $reference, PaiementRepository $paiementRepository): Response
    {
        $paiement = $paiementRepository->findOneBy(['reference' => $reference]);

        if (!$paiement) {
            throw $this->createNotFoundException('Paiement non trouvé avec cette référence');
        }

        return $this->redirectToRoute('app_paiement_show', ['id' => $paiement->getId()]);
    }

    #[Route('/statistiques', name: 'app_paiement_stats', methods: ['GET'])]
    public function statistiques(PaiementRepository $paiementRepository): Response
    {
        $paiements = $paiementRepository->findAll();

        $totalMontant = 0;
        $paiementsValides = 0;

        foreach ($paiements as $paiement) {
            if ($paiement->getStatut() === 'valide') {
                $totalMontant += (float)$paiement->getMontant();
                $paiementsValides++;
            }
        }

        return $this->render('paiement/statistiques.html.twig', [
            'total_paiements' => count($paiements),
            'paiements_valides' => $paiementsValides,
            'total_montant' => $totalMontant,
            'paiements' => $paiements,
        ]);
    }
}
