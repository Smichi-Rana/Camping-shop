<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        // On récupère les 4 derniers produits ajoutés pour la section "Meilleures Ventes" ou "Nouveautés"
        // Cela permet d'alimenter la boucle {% for product in products %} de ton template d'accueil
        $lastProducts = $productRepository->findBy([], ['id' => 'DESC'], 4);

        // On récupère toutes les catégories pour éventuellement les afficher dans un menu ou des badges
        $categories = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $lastProducts,
            'categories' => $categories,
        ]);
    }
}