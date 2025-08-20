<?php

namespace App\Tests\Service;

use App\Service\CartService;
use PHPUnit\Framework\TestCase;

class CartServiceTest extends TestCase
{
    public function testCartIsEmptyOnCreation(): void
    {
        $cart = new CartService();
        $this->assertEquals(0, $cart->getTotal());
    }

    public function testAddItemIncreasesTotalAccordingly(): void
    {
        $cart = new CartService();
        $cart->addItem(1, 2, 10.0); // 2 articles à 10€ chacun

        $this->assertEquals(20.0, $cart->getTotal());
    }

    public function testAddSameItemTwiceIncreasesQuantityAndTotal(): void
    {
        $cart = new CartService();
        $cart->addItem(1, 1, 10.0);
        $cart->addItem(1, 2, 10.0);

        $this->assertEquals(30.0, $cart->getTotal());
    }

    public function testAddDifferentItemsCalculatesTotalCorrectly(): void
    {
        $cart = new CartService();
        $cart->addItem(1, 1, 10.0); // 10
        $cart->addItem(2, 2, 5.0);  // 10

        $this->assertEquals(20.0, $cart->getTotal());
    }

    public function testRemoveItemSetsTotalToZero(): void
    {
        $cart = new CartService();
        $cart->addItem(1, 1, 10.0);
        $cart->removeItem(1);

        $this->assertEquals(0, $cart->getTotal());
    }

    public function testRemoveNonExistentItemDoesNotBreakCart(): void
    {
        $cart = new CartService();
        $cart->addItem(1, 1, 10.0);

        $cart->removeItem(999); // id inexistant

        $this->assertEquals(10.0, $cart->getTotal());
    }

    public function testAddItemWithZeroQuantityDoesNotAffectCart(): void
    {
        $cart = new CartService();
        $cart->addItem(1, 0, 10.0);

        $this->assertEquals(0, $cart->getTotal());
    }
}
