<?php

namespace App\Tests\Service;

use App\Service\CartService;
use PHPUnit\Framework\TestCase;

class CartServiceTest extends TestCase
{
    public function testAddItemAndCalculateTotal()
    {
        $cart = new CartService();
        $cart->addItem(1, 2, 10.0); // id=1, qty=2, price=10€

        $this->assertEquals(20.0, $cart->getTotal());
    }

    public function testRemoveItem()
    {
        $cart = new CartService();
        $cart->addItem(1, 1, 10.0);
        $cart->removeItem(1);

        $this->assertEquals(0, $cart->getTotal());
    }
}
