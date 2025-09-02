<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    public function __construct(private SessionInterface $session) {}

    /**
     * Ajoute un produit au panier en session
     */
    public function add(Product $product, string $size): void
    {
        $cart = $this->session->get('cart', []);
        $key = $product->getId() . '_' . $size;

        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = [
                'product' => $product,
                'size' => $size,
                'quantity' => 1
            ];
        }

        $this->session->set('cart', $cart);
    }

    /**
     * Supprime un article du panier
     */
    public function remove(string $key): void
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            $this->session->set('cart', $cart);
        }
    }

    /**
     * Retourne les items complets du panier
     */
    public function getFullCart(): array
    {
        return $this->session->get('cart', []);
    }

    /**
     * Calcule le total du panier
     */
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getFullCart() as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    /**
     * Vide le panier
     */
    public function clear(): void
    {
        $this->session->remove('cart');
    }

    /**
     * Simule le paiement et vide le panier (Stripe sandbox)
     */
    public function checkout(): void
    {
        // Ici on pourrait appeler l'API Stripe en mode test
        // Pour l'instant on vide juste le panier
        $this->clear();
    }
}
