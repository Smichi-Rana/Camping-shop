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

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
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

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Auto fields
            $reclamation->setUser($this->getUser());
            $reclamation->setDate(new \DateTimeImmutable());
            $reclamation->setStatut('En attente');

            // Commande
            $commande = $form->get('commande')->getData();
            $reclamation->setCommande($commande);

            // Save
            $em->persist($reclamation);
            $em->flush();

            // ------------ EMAIL ADMIN ---------------
            $email = (new Email())
                ->from('no-reply@tonsite.tn')
                ->to('admin@example.com') // changer par ton admin
                ->subject('Nouvelle réclamation #' . $reclamation->getId())
                ->html($this->renderView('emails/reclamation_new.html.twig', [
                    'reclamation' => $reclamation
                ]));

            $mailer->send($email);
            // ----------------------------------------

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

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

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande = $form->get('commande')->getData();
            $reclamation->setCommande($commande);

            $em->flush();

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/edit.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $em->remove($reclamation);
            $em->flush();
        }

        return $this->redirectToRoute('app_reclamation_index');
    }
}
