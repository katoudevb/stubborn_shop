<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartService $cartService): Response
    {
        $cartItems = $cartService->getFullCart();
        $total = $cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request, CartService $cartService): Response
    {
        $size = $request->request->get('size');
        if (!$size) {
            $this->addFlash('error', 'Veuillez sélectionner une taille.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        $cartService->add($product, $size);
        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(string $id, CartService $cartService, Request $request): Response
    {
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('remove_cart_item_'.$id, $token)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('cart');
        }

        $cartService->remove($id);
        $this->addFlash('success', 'Article supprimé du panier.');

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(CartService $cartService, Request $request): Response
    {
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('checkout_cart', $token)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('cart');
        }

        // Ici on simule le paiement (Stripe sandbox)
        $cartService->checkout();

        $this->addFlash('success', 'Commande finalisée avec succès (mode test).');
        return $this->redirectToRoute('app_products');
    }
}
