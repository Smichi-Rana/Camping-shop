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
            // Filtrer par email de l'utilisateur connecté
            $reclamations = $reclamationRepository->findBy([
                'emailUtilisateur' => $user->getEmail()
            ]);
        }

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    // Créer une nouvelle réclamation (CLIENT)
    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        // Créer la réclamation et PRÉ-REMPLIR les données
        $reclamation = new Reclamation();
        $reclamation->setNomUtilisateur($user->getFirstName() . ' ' . $user->getLastName());
        $reclamation->setEmailUtilisateur($user->getEmail());

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les infos sont déjà dans l'objet
            $reclamation->setDateCreation(new \DateTime());
            $reclamation->setStatut('en_attente'); // ⚠️ Minuscules + underscore

            $em->persist($reclamation);
            $em->flush();

            // Email admin
            try {
                $email = (new Email())
                    ->from('no-reply@tonsite.tn')
                    ->to('admin@example.com')
                    ->subject('Nouvelle réclamation #' . $reclamation->getId())
                    ->html($this->renderView('emails/reclamation_new.html.twig', [
                        'reclamation' => $reclamation
                    ]));

                $mailer->send($email);
            } catch (\Exception $e) {
                // Ignorer l'erreur d'email
            }

            $this->addFlash('success', '✅ Votre réclamation a été envoyée avec succès !');

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
        $user = $this->getUser();

        // Vérifier si l'utilisateur a le droit de voir cette réclamation
        if (!$this->isGranted('ROLE_ADMIN') && $reclamation->getEmailUtilisateur() !== $user->getEmail()) {
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
            // Exemple : l'admin peut changer le statut
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
    public function traiterPage(
        Reclamation $reclamation,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Vérifier si la réclamation est déjà traitée
        if ($reclamation->getStatut() !== 'En attente') {
            $this->addFlash('warning', 'Cette réclamation a déjà été traitée.');
            return $this->redirectToRoute('app_reclamation_index');
        }

        if ($request->isMethod('POST')) {
            $reponse = trim($request->request->get('reponse'));

            // Validation de la réponse
            if (empty($reponse)) {
                $this->addFlash('error', 'La réponse ne peut pas être vide.');
                return $this->redirectToRoute('app_reclamation_traiter_page', ['id' => $reclamation->getId()]);
            }

            // Mettre à jour la réclamation
            $reclamation->setReponse($reponse);
            $reclamation->setStatut('Traitée');
            $reclamation->setDateTraitement(new \DateTime());
            $em->flush();

            // Envoyer un email au client pour l'informer
            try {
                $email = (new Email())
                    ->from('no-reply@tonsite.tn')
                    ->to($reclamation->getEmailUtilisateur())
                    ->subject('Réponse à votre réclamation #' . $reclamation->getId())
                    ->html($this->renderView('emails/reclamation_reponse.html.twig', [
                        'reclamation' => $reclamation,
                        'reponse' => $reponse
                    ]));

                $mailer->send($email);

                $this->addFlash('success', 'La réclamation a été traitée et le client a été notifié par email.');
            } catch (\Exception $e) {
                $this->addFlash('warning', 'La réclamation a été traitée mais l\'email n\'a pas pu être envoyé.');
            }

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/traiter.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
}
