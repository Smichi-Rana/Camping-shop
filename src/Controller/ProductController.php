<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    // =========================
    // ADMIN
    // =========================

    #[Route('/admin/product', name: 'app_product')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté !');
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'form' => $form->createView(),
            'editing' => false,
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit(
        Product $product,
        Request $request,
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Produit modifié !');

            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'form' => $form->createView(),
            'editing' => true,
            'productEditing' => $product,
        ]);
    }

    #[Route('/admin/product/{id}/delete', name: 'product_delete')]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé !');
        return $this->redirectToRoute('app_product');
    }

    // =========================
    // FRONT (Routes publiques)
    // =========================

    #[Route('/', name: 'app_home')]
    public function home(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        return $this->render('product/list.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'featured' => $productRepository->findBy([], ['id' => 'DESC'], 8),
        ]);
    }

    #[Route('/products', name: 'app_product_index')]
    public function productIndex(
        Request $request,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $search = $request->query->get('search', '');
        
        if ($search) {
            $products = $productRepository->createQueryBuilder('p')
                ->where('p.name LIKE :search')
                ->orWhere('p.description LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            $products = $productRepository->findAll();
        }

        return $this->render('product/list.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
            'search' => $search,
        ]);
    }

    #[Route('/products/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(
        Product $product,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ): Response {
        // Produits similaires de la même catégorie
        $relatedProducts = $productRepository->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.id != :productId')
            ->setParameter('category', $product->getCategory())
            ->setParameter('productId', $product->getId())
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'categories' => $categoryRepository->findAll(),
            'relatedProducts' => $relatedProducts,
        ]);
    }

    #[Route('/category/{id}/products', name: 'app_product_category', requirements: ['id' => '\d+'])]
    public function productsByCategory(
        int $id,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ): Response {
        $category = $categoryRepository->find($id);
        
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('category/products.html.twig', [
            'category' => $category,
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}