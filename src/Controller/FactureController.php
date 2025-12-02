<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/facture')]
class FactureController extends AbstractController
{
    // Liste toutes les factures
    #[Route('/', name: 'facture_index', methods: ['GET'])]
    public function index(FactureRepository $repo): Response
    {
        $factures = $repo->findAll();
        return $this->render('facture/index.html.twig', [
            'factures' => $factures
        ]);
    }

    // Afficher une facture
    #[Route('/{id}', name: 'facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', [
            'facture' => $facture
        ]);
    }

    // Modifier une facture si nécessaire
    #[Route('/{id}/edit', name: 'facture_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Facture $facture, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Facture mise à jour.');
            return $this->redirectToRoute('facture_index');
        }

        return $this->render('facture/edit.html.twig', [
            'form' => $form->createView(),
            'facture' => $facture
        ]);
    }

    // Supprimer une facture
    #[Route('/{id}/delete', name: 'facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $em->remove($facture);
            $em->flush();
            $this->addFlash('success', 'Facture supprimée.');
        }

        return $this->redirectToRoute('facture_index');
    }
}
