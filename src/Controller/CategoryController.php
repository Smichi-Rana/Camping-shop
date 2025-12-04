<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    #[Route('/category/{id}/edit', name: 'category_edit')]
    public function index(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository, Category $category = null): Response
    {
        // Si pas de catégorie fournie, on crée un nouveau
        if (!$category) {
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', $category->getId() ? 'Catégorie modifiée !' : 'Catégorie ajoutée !');

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'form' => $form->createView(),
            'editing' => $category->getId() !== null,
        ]);
    }

    #[Route('/category/{id}/delete', name: 'category_delete')]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'Catégorie supprimée !');

        return $this->redirectToRoute('app_category');
    }
}
