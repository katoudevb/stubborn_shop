<?php

namespace App\Tests\Service;

use App\Service\CartService;
use App\Service\OrderService;
use App\Entity\Order;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    private CartService $cart;
    private OrderService $orderService;

    protected function setUp(): void
    {
        // Initialise un panier vide
        $this->cart = new CartService();

        // Initialise le service de commande
        // Si ton OrderService a besoin de Doctrine, tu peux utiliser un mock
        $this->orderService = new OrderService();
    }

    public function testPlaceOrderWithItems(): void
    {
        // Ajout de produits dans le panier
        $this->cart->addItem(1, 2, 10.0); // 2 articles à 10€
        $this->cart->addItem(2, 1, 20.0); // 1 article à 20€

        // Passe la commande
        $order = $this->orderService->placeOrder($this->cart);

        // Vérifie que l'objet Order est créé
        $this->assertInstanceOf(Order::class, $order);

        // Vérifie le total
        $expectedTotal = 2*10 + 1*20; // 40
        $this->assertEquals($expectedTotal, $order->getTotal());

        // Vérifie que le panier est vidé après achat
        $this->assertEquals(0, $this->cart->getTotal());
    }

    public function testPlaceOrderWithEmptyCartThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Le panier est vide');

        // Tente de passer commande sur un panier vide
        $this->orderService->placeOrder($this->cart);
    }
}
