<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(
        SessionInterface $session,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $cart = $session->get('cart', []);
        $cartWithData = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->getPrice() * $quantity
                ];
                $total += $product->getPrice() * $quantity;
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(
        int $id,
        SessionInterface $session,
        ProductRepository $productRepository
    ): JsonResponse {
        $product = $productRepository->find($id);
        
        if (!$product) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        $cart = $session->get('cart', []);
        
        if (isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

        return new JsonResponse([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'cartCount' => array_sum($cart)
        ]);
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(
        int $id,
        SessionInterface $session
    ): Response {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
        $this->addFlash('success', 'Produit retiré du panier');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(
        int $id,
        Request $request,
        SessionInterface $session
    ): JsonResponse {
        $quantity = (int) $request->request->get('quantity', 1);
        
        if ($quantity < 1) {
            return new JsonResponse(['error' => 'Quantité invalide'], 400);
        }

        $cart = $session->get('cart', []);
        
        if (isset($cart[$id])) {
            $cart[$id] = $quantity;
        }

        $session->set('cart', $cart);

        return new JsonResponse([
            'success' => true,
            'message' => 'Panier mis à jour'
        ]);
    }

    #[Route('/clear', name: 'app_cart_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');
        $this->addFlash('success', 'Panier vidé');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/count', name: 'app_cart_count', methods: ['GET'])]
    public function count(SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);
        
        return new JsonResponse([
            'count' => array_sum($cart)
        ]);
    }
}