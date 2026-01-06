<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    ): Response {
        $product = $productRepository->find($id);
        
        if (!$product) {
            $this->addFlash('danger', 'Produit non trouvé');
            return $this->redirectToRoute('app_product_index');
        }

        $cart = $session->get('cart', []);
        
        if (isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);
        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('app_cart');
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
    ): Response {
        $quantity = (int) $request->request->get('quantity');
        $cart = $session->get('cart', []);
        
        if (isset($cart[$id])) {
            if ($quantity > 0) {
                $cart[$id] = $quantity;
            } else {
                unset($cart[$id]);
            }
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/clear', name: 'app_cart_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');
        $this->addFlash('success', 'Panier vidé');
        return $this->redirectToRoute('app_cart');
    }

    /* --- NOUVELLE ROUTE : PAIEMENT / CHECKOUT --- */
    #[Route('/checkout', name: 'app_cart_checkout')]
    public function checkout(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        if (empty($cart)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_product_index');
        }

        // Pour l'instant, on vide le panier après le clic sur paiement 
        // pour simuler une commande réussie
        $session->remove('cart');
        
        return $this->render('cart/checkout.html.twig');
    }
}