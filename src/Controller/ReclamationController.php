<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    // Liste des réclamations
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $reclamations = $reclamationRepository->findAll();
        } else {
            $reclamations = $reclamationRepository->findBy(['user' => $user]);
        }

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    // Créer une nouvelle réclamation (CLIENT)
    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setUser($this->getUser());
            $reclamation->setDate(new \DateTimeImmutable());
            $reclamation->setStatut('En attente');

            $em->persist($reclamation);
            $em->flush();

            // Email admin
            $email = (new Email())
                ->from('no-reply@tonsite.tn')
                ->to('admin@example.com') // email de l'admin
                ->subject('Nouvelle réclamation #' . $reclamation->getId())
                ->html($this->renderView('emails/reclamation_new.html.twig', [
                    'reclamation' => $reclamation
                ]));

            $mailer->send($email);

            $this->addFlash('success', 'Votre réclamation a été envoyée.');

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Voir une réclamation
    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $reclamation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    // Editer la réclamation (seulement admin pour changer le statut)
    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Exemple : l’admin peut changer le statut
            $statut = $request->request->get('statut');
            if ($statut) {
                $reclamation->setStatut($statut);
            }

            $em->flush();

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/edit.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    // Supprimer une réclamation
    #[Route('/{id}/delete', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $em->remove($reclamation);
            $em->flush();
        }

        return $this->redirectToRoute('app_reclamation_index');
    }

    #[Route('/{id}/traiter-page', name: 'app_reclamation_traiter_page', methods: ['GET', 'POST'])]
    public function traiterPage(Reclamation $reclamation, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            // récupérer la réponse de l'admin depuis le formulaire
            $reclamation->setReponse($request->request->get('reponse'));
            $reclamation->setStatut('Traitée');
            $em->flush();

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/traiter.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }


}

