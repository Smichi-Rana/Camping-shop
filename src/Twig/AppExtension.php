<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private CategoryRepository $categoryRepository;
    private RequestStack $requestStack;

    public function __construct(
        CategoryRepository $categoryRepository,
        RequestStack $requestStack
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->requestStack = $requestStack;
    }

    public function getGlobals(): array
    {
        return [
            'categories' => $this->categoryRepository->findAll(),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cart_count', [$this, 'getCartCount']),
        ];
    }

    public function getCartCount(): int
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        
        return array_sum($cart);
    }
}