<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Calcule le total du panier d'un utilisateur
     */
    public function calculateTotal(User $user): float
    {
        $total = 0;

        foreach ($user->getCarts() as $cartItem) {
            $total += $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
        }

        return $total;
    }

    /**
     * Supprime un article du panier
     */
    public function removeItem(User $user, int $cartItemId): void
    {
        $cartItem = $this->em->getRepository(Cart::class)->find($cartItemId);

        if ($cartItem && $cartItem->getUser() === $user) {
            $this->em->remove($cartItem);
            $this->em->flush();
        }
    }

    /**
     * Vide complètement le panier de l'utilisateur
     */
    public function clearCart(User $user): void
    {
        foreach ($user->getCarts() as $cartItem) {
            $this->em->remove($cartItem);
        }
        $this->em->flush();
    }

    /**
     * Valide le panier : crée une commande avec ses lignes puis vide le panier
     */
    public function checkout(User $user): Order
    {
        if ($user->getCarts()->isEmpty()) {
            throw new \LogicException('Le panier est vide.');
        }

        // Création de la commande
        $order = new Order();
        $order->setUser($user);
        $order->setTotal($this->calculateTotal($user));
        $order->setCreatedAt(new \DateTimeImmutable());

        // Copie des articles du panier dans la commande
        foreach ($user->getCarts() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($cartItem->getProduct()->getPrice());

            $this->em->persist($orderItem);
            $this->em->remove($cartItem); // on supprime du panier
        }

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }
}
