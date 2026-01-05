<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {
        // Récupérer le terme de recherche
        $searchTerm = $request->query->get('search', '');

        if ($searchTerm) {
            $articles = $articleRepository->findByNameLike($searchTerm);
        } else {
            $articles = $articleRepository->findAll();
        }

        return $this->render('produit/index.html.twig', [
            'articles' => $articles,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la photo
            $photo = $form->get('photo')->getData();
            if ($photo) {
                try {
                    $photoName = uniqid() . '.' . $photo->guessExtension();
                    $photo->move($this->getParameter('photos_directory'), $photoName);
                    $article->setPhoto($photoName);
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors du téléchargement de la photo.');
                    return $this->redirectToRoute('app_produit_index');
                }
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès!');
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('produit/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Article $article,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la photo
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Supprimer l'ancienne photo si elle existe
                if ($article->getPhoto()) {
                    $oldPhotoPath = $this->getParameter('photos_directory') . '/' . $article->getPhoto();
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }

                try {
                    $photoName = uniqid() . '.' . $photo->guessExtension();
                    $photo->move($this->getParameter('photos_directory'), $photoName);
                    $article->setPhoto($photoName);
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors du téléchargement de la photo.');
                    return $this->redirectToRoute('app_produit_edit', ['id' => $article->getId()]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès!');
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Article $article,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            // Supprimer la photo si elle existe
            if ($article->getPhoto()) {
                $photoPath = $this->getParameter('photos_directory') . '/' . $article->getPhoto();
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Produit supprimé avec succès!');
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
