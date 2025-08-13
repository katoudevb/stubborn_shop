<?php

namespace App\Service;

class CartService
{
    private array $items = [];

    public function addItem(int $id, int $quantity, float $price): void
    {
        $this->items[$id] = ['quantity' => $quantity, 'price' => $price];
    }

    public function removeItem(int $id): void
    {
        unset($this->items[$id]);
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['quantity'] * $item['price'];
        }
        return $total;
    }
}
