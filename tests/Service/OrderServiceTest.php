<?php

namespace App\Tests\Service;

use App\Service\CartService;
use App\Service\OrderService;
use App\Entity\Order;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    private CartService $cart;
    private OrderService $orderService;

    protected function setUp(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\App\Repository\ProductRepository $repoMock */
        $repoMock = $this->createMock(ProductRepository::class);

        $this->cart = new CartService($repoMock);
        $this->orderService = new OrderService($this->cart);
    }

    public function testPlaceOrderWithItems(): void
    {
        $this->cart->addItem(1, 2, 10.0);
        $this->cart->addItem(2, 1, 20.0);

        $order = $this->orderService->placeOrder($this->cart);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(40.0, $order->getTotal());
        $this->assertEquals(0, $this->cart->getTotal());
    }

    public function testPlaceOrderWithEmptyCartThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Le panier est vide');

        $this->orderService->placeOrder($this->cart);
    }
}
