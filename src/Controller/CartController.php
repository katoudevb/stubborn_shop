<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $product = $em->getRepository(Product::class)->find($id);
        if (!$product) {
            return new Response('Produit introuvable', 404);
        }

        // Chercher un panier actif ou en créer un
        $cart = $em->getRepository(Cart::class)->findOneBy([
            'user' => $user,
            'isFinalized' => false
        ]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user)
                 ->setIsFinalized(false)
                 ->setCreatedAt(new \DateTimeImmutable());

            $em->persist($cart);
            $em->flush(); // on flush pour donner un ID au panier
        }

        // Vérifier si le produit existe déjà dans le panier
        $existingItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product)
                     ->setQuantity(1)
                     ->setSize('M'); // Taille par défaut

            // 🔹 Gère les deux côtés de la relation
            $cart->addCartItem($cartItem);

            $em->persist($cartItem);
        }

        $em->flush();

        return new Response('Produit ajouté au panier');
    }
}
