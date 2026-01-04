<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CartCountExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cart_count', [$this, 'getCartCount']),
        ];
    }

    public function getCartCount(): int
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return 0;
        }

        // Utiliser les cookies au lieu de la session
        $cart = json_decode($request->cookies->get('cart', '{}'), true);

        return array_sum($cart);
    }
}
