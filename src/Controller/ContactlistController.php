<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactlistController extends AbstractController
{
    #[Route('/contactlist', name: 'contactlist')]
    public function index(EntityManagerInterface $em): Response
    {
        $contacts = $em->getRepository(Contact::class)->findAll();

        return $this->render('admin/contactlist.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/delete_contacts', name: 'delete_contacts', methods: ['POST'])]
    public function deleteSelectedContacts(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer les valeurs transmises pour 'selected_contacts' comme tableau
        $selectedContacts = $request->request->all('selected_contacts');

        // Vérifier que $selectedContacts est bien un tableau
        if (!is_array($selectedContacts)) {
            $this->addFlash('warning', 'Invalid data received.');
            return $this->redirectToRoute('contactlist');
        }

        // Filtrer pour s'assurer que toutes les valeurs sont des entiers valides
        $filteredContacts = array_filter($selectedContacts, function ($value) {
            return is_numeric($value) && filter_var($value, FILTER_VALIDATE_INT);
        });

        if (!empty($filteredContacts)) {
            // Récupérer les contacts correspondants à ces IDs
            $contactsToDelete = $em->getRepository(Contact::class)->findBy(['id' => $filteredContacts]);

            foreach ($contactsToDelete as $contact) {
                $em->remove($contact);
            }

            $em->flush();

            $this->addFlash('success', 'Selected contacts successfully deleted.');





        } else {
            $this->addFlash('warning', 'No valid contact selected.');
        }

        return $this->redirectToRoute('contactlist');
    }

}
