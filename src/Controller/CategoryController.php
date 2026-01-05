<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    // ============================================
    // PARTIE PUBLIQUE - Affichage des produits
    // ============================================
    
    #[Route('/category/{id}/products', name: 'app_product_category')]
    public function products(Category $category, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('category/products.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    // ============================================
    // PARTIE ADMIN - Gestion des catégories
    // ============================================
    
    #[Route('/admin/category', name: 'admin_category_index')]
    public function adminIndex(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/category/create', name: 'admin_category_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $emoji = $request->request->get('emoji');

        if (!$name) {
            $this->addFlash('error', 'Le nom de la catégorie est obligatoire.');
            return $this->redirectToRoute('admin_category_index');
        }

        $category = new Category();
        $category->setName($name);
        $category->setDescription($description);
        $category->setEmoji($emoji ?: '📦');

        $em->persist($category);
        $em->flush();

        $this->addFlash('success', 'La catégorie "' . $name . '" a été créée avec succès.');

        return $this->redirectToRoute('admin_category_index');
    }

    #[Route('/admin/category/{id}/edit', name: 'admin_category_edit', methods: ['POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $emoji = $request->request->get('emoji');

        if (!$name) {
            $this->addFlash('error', 'Le nom de la catégorie est obligatoire.');
            return $this->redirectToRoute('admin_category_index');
        }

        $category->setName($name);
        $category->setDescription($description);
        $category->setEmoji($emoji);

        $em->flush();

        $this->addFlash('success', 'La catégorie a été mise à jour avec succès.');

        return $this->redirectToRoute('admin_category_index');
    }

    #[Route('/admin/category/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $name = $category->getName();

        // Vérifier s'il y a des produits liés
        if ($category->getProducts()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle contient des produits.');
            return $this->redirectToRoute('admin_category_index');
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'La catégorie "' . $name . '" a été supprimée.');

        return $this->redirectToRoute('admin_category_index');
    }
}