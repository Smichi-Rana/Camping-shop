<?php

namespace App\Controller;

use App\Entity\AvisClient;
use App\Form\AvisClientType;
use App\Repository\AvisClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/avis')]
class AvisController extends AbstractController
{
    #[Route('/list', name: 'avis_index')]
    public function index(AvisClientRepository $avisRepo): Response
    {
        $avis = $avisRepo->findAll();

        return $this->render('avis/index.html.twig', [
            'avis' => $avis
        ]);
    }

    #[Route('/new', name: 'avis_new', methods:['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $avis = new AvisClient();
        $avis->setCreatedAt(new \DateTime());
        $avis->setUser($user);

        $form = $this->createForm(AvisClientType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($avis);
            $em->flush();
            $this->addFlash('success', 'Merci pour votre avis !');
            return $this->redirectToRoute('avis_index');
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
