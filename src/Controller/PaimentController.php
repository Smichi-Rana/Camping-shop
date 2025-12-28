<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Form\PaymentFormType;
use App\Repository\PaiementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/paiement')]
class PaimentController extends AbstractController
{
    // 🟢 Formulaire et sauvegarde du paiement
    #[Route('/new', name: 'paiement_new')]
    public function new(Request $request, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $paymentForm = $this->createForm(PaymentFormType::class);
        $paymentForm->handleRequest($request);

        if ($paymentForm->isSubmitted() && $paymentForm->isValid()) {
            $data = $paymentForm->getData();

            $paiement = new Paiement();
            $paiement->setMethode($data['methode']);
            $paiement->setMontant($data['montant']);
            $paiement->setStatut('pending'); // ou 'paid' selon ta logique
            $paiement->setDatePaiement(new \DateTime());
            $paiement->setUser($user); // si relation User dans l'entity

            $em = $this->getDoctrine()->getManager();
            $em->persist($paiement);
            $em->flush();

            $this->addFlash('success', 'Votre paiement a été enregistré avec succès.');
            return $this->redirectToRoute('paiement_new');
        }

        return $this->render('paiement/index.html.twig', [
            'user' => $user,
            'paymentForm' => $paymentForm->createView(),
        ]);
    }

    // 🟢 Liste des paiements (admin)
    #[Route('/list', name: 'paiement_list')]
    public function list(PaiementRepository $paiementRepository): Response
    {
        $paiements = $paiementRepository->findAll();

        return $this->render('paiement/list.html.twig', [
            'paiements' => $paiements
        ]);
    }
}
