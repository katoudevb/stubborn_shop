<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    // Déclare la fonction Twig existante
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active_route', [$this, 'isActiveRoute']),
        ];
    }

    // Déclare les variables globales Twig
    public function getGlobals(): array
    {
        return [
            'companyInfo' => [
                'name' => 'Stubborn',
                'address' => 'Piccadilly Circus, London W1J 0DA, Royaume-Uni',
                'email' => 'stubborn@blabla.com',
                'slogan' => "Don't compromise on your look"
            ]
        ];
    }

    // Méthode existante
    public function isActiveRoute(string $routeName, bool $startsWith = false): bool
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        return $startsWith ? str_starts_with($currentRoute, $routeName) : $currentRoute === $routeName;
    }
}
