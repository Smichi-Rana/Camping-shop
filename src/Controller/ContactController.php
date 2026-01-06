<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ContactController extends AbstractController
{
    /**
     * Page de contact avec formulaire
     */
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $contact = new Contact();

        // Si l'utilisateur est connecté, pré-remplir ses informations
        $user = $this->getUser();
        if ($user) {
            $contact->setNom($user->getLastName());
            $contact->setPrenom($user->getFirstName());
            $contact->setEmail($user->getEmail());
            if (method_exists($user, 'getPhone')) {
                $contact->setTelephone($user->getPhone());
            }
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Ajouter la date de création et le statut
                if (method_exists($contact, 'setDateCreation')) {
                    $contact->setDateCreation(new \DateTimeImmutable());
                }
                if (method_exists($contact, 'setStatut')) {
                    $contact->setStatut('Non lu');
                }

                // Associer l'utilisateur si connecté
                if ($user && method_exists($contact, 'setUser')) {
                    $contact->setUser($user);
                }

                $em->persist($contact);
                $em->flush();

                // Message de succès en français
                $this->addFlash('success', '✅ Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');

                // Redirection pour éviter la double soumission
                return $this->redirectToRoute('app_contact');

            } catch (\Exception $e) {
                $this->addFlash('error', '❌ Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.');
            }
        }

        return $this->render('client/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Liste des messages de contact (Admin uniquement)
     */
    #[Route('/admin/contacts', name: 'app_contact_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function list(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findBy([], ['id' => 'DESC']);

        // Statistiques simples
        $stats = [
            'total' => count($contacts),
            'non_lus' => 0,
            'traites' => 0,
        ];

        foreach ($contacts as $contact) {
            if (method_exists($contact, 'getStatut')) {
                $statut = $contact->getStatut();
                if ($statut === 'Non lu') {
                    $stats['non_lus']++;
                } elseif ($statut === 'Traité') {
                    $stats['traites']++;
                }
            }
        }

        return $this->render('admin/contact_list.html.twig', [
            'contacts' => $contacts,
            'stats' => $stats,
        ]);
    }

    /**
     * Voir les détails d'un message de contact (Admin)
     */
    #[Route('/admin/contact/{id}', name: 'app_contact_detail')]
    #[IsGranted('ROLE_ADMIN')]
    public function detail(int $id, ContactRepository $contactRepository, EntityManagerInterface $em): Response
    {
        $contact = $contactRepository->find($id);

        if (!$contact) {
            $this->addFlash('error', 'Message non trouvé');
            return $this->redirectToRoute('app_contact_list');
        }

        // Marquer comme lu automatiquement
        if (method_exists($contact, 'getStatut') && method_exists($contact, 'setStatut')) {
            if ($contact->getStatut() === 'Non lu') {
                $contact->setStatut('Lu');
                $em->flush();
            }
        }

        return $this->render('admin/contact_detail.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * Marquer un message comme traité (Admin)
     */
    #[Route('/admin/contact/{id}/mark-treated', name: 'app_contact_mark_treated', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function markAsTreated(int $id, ContactRepository $contactRepository, EntityManagerInterface $em): Response
    {
        $contact = $contactRepository->find($id);

        if (!$contact) {
            $this->addFlash('error', 'Message non trouvé');
            return $this->redirectToRoute('app_contact_list');
        }

        if (method_exists($contact, 'setStatut')) {
            $contact->setStatut('Traité');
            $em->flush();
            $this->addFlash('success', 'Message marqué comme traité');
        }

        return $this->redirectToRoute('app_contact_list');
    }

    /**
     * Supprimer un message de contact (Admin)
     */
    #[Route('/admin/contact/{id}/delete', name: 'app_contact_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id, ContactRepository $contactRepository, EntityManagerInterface $em): Response
    {
        $contact = $contactRepository->find($id);

        if (!$contact) {
            $this->addFlash('error', 'Message non trouvé');
            return $this->redirectToRoute('app_contact_list');
        }

        $em->remove($contact);
        $em->flush();

        $this->addFlash('success', 'Message supprimé avec succès');
        return $this->redirectToRoute('app_contact_list');
    }
}
