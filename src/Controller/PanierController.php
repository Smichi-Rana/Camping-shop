<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;



class PanierController extends AbstractController
{

    #[Route('/cart', name: 'cart')]
    public function index(SessionInterface $session, ArticleRepository $articleRepository): Response
    {
        $cart = $session->get('cart', []);

        // Récupérer les articles du panier
        $cartWithDetails = [];
        foreach ($cart as $id => $quantity) {
            $article = $articleRepository->find($id);
            if ($article) {
                $cartWithDetails[] = [
                    'article' => $article,
                    'quantity' => $quantity,
                ];
            }
        }

        return $this->render('panier/index.html.twig', [
            'cart' => $cartWithDetails,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (!isset($cart[$id])) {
            $cart[$id] = 0;
        }
        $cart[$id]++;
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/admin/orders', name: 'admin_orders')]
    public function listOrders(SessionInterface $session, ArticleRepository $articleRepository): Response
    {
        $cart = $session->get('cart', []);

        // Récupérer les articles du panier
        $cartWithDetails = [];
        foreach ($cart as $id => $quantity) {
            $article = $articleRepository->find($id);
            if ($article) {
                $cartWithDetails[] = [
                    'article' => $article,
                    'quantity' => $quantity,
                ];
            }
        }

        return $this->render('admin/orders.html.twig', [
            'cart' => $cartWithDetails,
        ]);
    }

}
