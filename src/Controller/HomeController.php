<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // Générer 100 produits de camping
    private function generateProducts(): array
    {
        $categories = ['Tentes', 'Sacs de couchage', 'Réchauds', 'Lampes', 'Ustensiles', 'Vêtements', 'Accessoires'];
        $brands = ['CampMaster', 'OutdoorPro', 'NatureTrek', 'AdventureGear', 'WildCamp'];

        $products = [];
        for ($i = 1; $i <= 100; $i++) {
            $category = $categories[array_rand($categories)];
            $brand = $brands[array_rand($brands)];
            $rating = round(rand(35, 50) / 10, 1);

            $products[] = [
                'id' => $i,
                'name' => $category . ' ' . $brand . ' Model ' . $i,
                'description' => 'Produit de camping de haute qualité, parfait pour vos aventures en plein air.',
                'price' => rand(20, 500),
                'rating' => $rating,
                'category' => $category,
                'brand' => $brand,
                'stock' => rand(5, 50),
                'image' => 'https://scontent.ftun10-1.fna.fbcdn.net/v/t39.30808-6/499717352_9863637800391583_7562069709485482853_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=ZQRPFfoziskQ7kNvwEUT8TL&_nc_oc=Adm1szkpX276Fky2VAxq8ArlQzivW48BRMRJbRlxYYMkUfb60tj6lBogrX2rRpti4UE&_nc_zt=23&_nc_ht=scontent.ftun10-1.fna&_nc_gid=pX8s8EajYczGLTSCvdTaKQ&oh=00_AfqoAD54uVdRWM883So0i0Ry7sBIYxsaP4-GvosNeb1MmQ&oe=6960E582'
            ];
        }

        return $products;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $products = $this->generateProducts();

        // Récupérer le terme de recherche
        $searchTerm = $request->query->get('search', '');

        // Filtrer les produits si une recherche est effectuée
        if ($searchTerm) {
            $products = array_filter($products, function($product) use ($searchTerm) {
                $searchLower = strtolower($searchTerm);
                return
                    stripos($product['name'], $searchTerm) !== false ||
                    stripos($product['category'], $searchTerm) !== false ||
                    stripos($product['brand'], $searchTerm) !== false ||
                    stripos($product['description'], $searchTerm) !== false;
            });
            // Réindexer le tableau après le filtrage
            $products = array_values($products);
        }

        // Pagination
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 12; // 12 produits par page
        $offset = ($page - 1) * $limit;

        $totalProducts = count($products);
        $totalPages = ceil($totalProducts / $limit);
        $currentProducts = array_slice($products, $offset, $limit);

        return $this->render('home/index.html.twig', [
            'products' => $currentProducts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'searchTerm' => $searchTerm
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(int $id): Response
    {
        $products = $this->generateProducts();

        // Trouver le produit par ID
        $product = null;
        foreach ($products as $p) {
            if ($p['id'] === $id) {
                $product = $p;
                break;
            }
        }

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('home/about.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('home/aboutRana.html.twig');
    }
}
