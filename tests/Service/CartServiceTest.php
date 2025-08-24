<?php

namespace App\Tests\Service;

use App\Service\CartService;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

class CartServiceTest extends TestCase
{
    private CartService $cart;

    protected function setUp(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\App\Repository\ProductRepository $repoMock */
        $repoMock = $this->createMock(ProductRepository::class);

        $this->cart = new CartService($repoMock);
    }

    public function testCartIsEmptyOnCreation(): void
    {
        $this->assertEquals(0, $this->cart->getTotal());
    }

    public function testAddItemIncreasesTotalAccordingly(): void
    {
        $this->cart->addItem(1, 2, 10.0);
        $this->assertEquals(20.0, $this->cart->getTotal());
    }

    public function testAddSameItemTwiceIncreasesQuantityAndTotal(): void
    {
        $this->cart->addItem(1, 1, 10.0);
        $this->cart->addItem(1, 2, 10.0);
        $this->assertEquals(30.0, $this->cart->getTotal());
    }

    public function testAddDifferentItemsCalculatesTotalCorrectly(): void
    {
        $this->cart->addItem(1, 1, 10.0);
        $this->cart->addItem(2, 2, 5.0);
        $this->assertEquals(20.0, $this->cart->getTotal());
    }

    public function testRemoveItemSetsTotalToZero(): void
    {
        $this->cart->addItem(1, 1, 10.0);
        $this->cart->removeItem(1);
        $this->assertEquals(0, $this->cart->getTotal());
    }

    public function testRemoveNonExistentItemDoesNotBreakCart(): void
    {
        $this->cart->addItem(1, 1, 10.0);
        $this->cart->removeItem(999);
        $this->assertEquals(10.0, $this->cart->getTotal());
    }

    public function testAddItemWithZeroQuantityDoesNotAffectCart(): void
    {
        $this->cart->addItem(1, 0, 10.0);
        $this->assertEquals(0, $this->cart->getTotal());
    }
}
