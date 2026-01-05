<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    // Liste pour ADMIN
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    // Mes commandes pour CLIENT
    #[Route('/mes-commandes', name: 'app_mes_commandes', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mesCommandes(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();

        return $this->render('commande/mes_commandes.html.twig', [
            'commandes' => $commandeRepository->findBy(['user' => $user], ['dateCommande' => 'DESC']),
        ]);
    }

    // Créer une commande (CLIENT)
    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $commande->setUser($this->getUser());
        $commande->setStatut('en attente');
        $commande->setDateCommande(new \DateTime());

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            $this->addFlash('success', 'Commande créée avec succès !');
            return $this->redirectToRoute('app_mes_commandes');
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    // Voir détails commande
    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Commande $commande): Response
    {
        // Vérifier que l'utilisateur peut voir cette commande
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') && $commande->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette commande.');
        }

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    // Valider une commande (ADMIN)
    #[Route('/{id}/valider', name: 'app_commande_valider', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function valider(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setStatut('validée');
        $entityManager->flush();

        $this->addFlash('success', 'Commande validée avec succès !');
        return $this->redirectToRoute('app_commande_index');
    }

    // Annuler une commande (ADMIN)
    #[Route('/{id}/annuler', name: 'app_commande_annuler', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function annuler(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setStatut('annulée');
        $entityManager->flush();

        $this->addFlash('warning', 'Commande annulée.');
        return $this->redirectToRoute('app_commande_index');
    }
}
