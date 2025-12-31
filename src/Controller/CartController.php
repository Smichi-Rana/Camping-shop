<?php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(Request $request, ProductRepository $repo): Response
    {
        $cart = json_decode($request->cookies->get('cart', '[]'), true);

        $data = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $repo->find($id);
            if ($product) {
                $data[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product->getPrix() * $quantity
                ];
                $total += $product->getPrix() * $quantity;
            }
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $data,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, Request $request): Response
    {
        $cart = json_decode($request->cookies->get('cart', '{}'), true);

        $cart[$id] = ($cart[$id] ?? 0) + 1;

        $response = $this->redirectToRoute('cart_index');
        $response->headers->setCookie(
            new Cookie('cart', json_encode($cart), time() + 3600)
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
            new Cookie('cart', json_encode($cart))
        );

        return $response;
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(): Response
    {
        $response = $this->redirectToRoute('cart_index');
        $response->headers->clearCookie('cart');
        return $response;
    }
}
