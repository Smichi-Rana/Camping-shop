<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /* ================= PRODUITS ================= */

    private function generateProducts(): array
    {
        $categories = ['Tentes', 'Sacs de couchage', 'Réchauds', 'Lampes', 'Ustensiles', 'Vêtements', 'Accessoires'];
        $brands = ['CampMaster', 'OutdoorPro', 'NatureTrek', 'AdventureGear', 'WildCamp'];

        $products = [];
        for ($i = 1; $i <= 100; $i++) {
            $category = $categories[array_rand($categories)];
            $brand = $brands[array_rand($brands)];

            $products[] = [
                'id' => $i,
                'name' => $category.' '.$brand.' Model '.$i,
                'description' => 'Produit de camping de haute qualité.',
                'price' => rand(20, 500),
                'rating' => round(rand(35, 50) / 10, 1),
                'category' => $category,
                'brand' => $brand,
                'stock' => rand(5, 50),
                'image' => 'https://picsum.photos/seed/'.$i.'/400/400'
            ];
        }

        return $products;
    }

    private function findProductById(int $id): ?array
    {
        foreach ($this->generateProducts() as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }
        return null;
    }

    /* ================= PANIER ================= */

    #[Route('/cart', name: 'cart_index')]
    public function index(Request $request): Response
    {
        $cart = json_decode($request->cookies->get('cart', '{}'), true);

        $items = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $product = $this->findProductById((int)$id);
            if ($product) {
                $subtotal = $product['price'] * $qty;
                $items[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }


        return $this->render('cart/index.html.twig', [
            'cartItems' => $items,
            'total' => $total,
            'itemCount' => array_sum($cart)
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(int $id, Request $request): Response
    {
        $product = $this->findProductById($id);
        if (!$product) {
            return $this->redirectToRoute('app_home');
        }

        $cart = json_decode($request->cookies->get('cart', '{}'), true);

        $cart[$id] = ($cart[$id] ?? 0) + 1;

        $response = $this->redirectToRoute('cart_index');
        $response->headers->setCookie(
            new Cookie('cart', json_encode($cart), strtotime('+30 days'), '/')
        );

        return $response;
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(int $id, Request $request): Response
    {
        $cart = json_decode($request->cookies->get('cart', '{}'), true);

        unset($cart[$id]);

        $response = $this->redirectToRoute('cart_index');
        $response->headers->setCookie(
            new Cookie('cart', json_encode($cart), strtotime('+30 days'), '/')
        );

        return $response;
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(): Response
    {
        $response = $this->redirectToRoute('cart_index');
        $response->headers->setCookie(
            Cookie::create('cart')->withValue('')->withExpires(1)
        );

        return $response;
    }
}
