<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;

class AppExtension extends AbstractExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active_route', [$this, 'isActiveRoute']),
        ];
    }

    public function isActiveRoute(string $routeName, bool $startsWith = false): bool
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        return $startsWith ? str_starts_with($currentRoute, $routeName) : $currentRoute === $routeName;
    }
}
