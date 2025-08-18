<?php

namespace App\Controller;

use App\Service\CartService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
final class CartController extends AbstractController
{
    #[Route('', name: 'app_cart', methods: ['GET'])]
    public function index(CartService $cartService): Response
    {
        $user = $this->getUser();

        return $this->render('cart/index.html.twig', [
            'cartItems' => $user->getCarts(),
            'total' => $cartService->calculateTotal($user),
        ]);
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id, Request $request, CartService $cartService): Response
    {
        if (!$this->isCsrfTokenValid('remove_cart_item_' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $cartService->removeItem($this->getUser(), $id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(CartService $cartService, StripeService $stripeService): Response
    {
        $user = $this->getUser();

        if ($user->getCarts()->isEmpty()) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $total = $cartService->calculateTotal($user);
        $amountInCents = intval($total * 100);

        $paymentResult = $stripeService->simulatePayment($amountInCents, 'eur');

        if ($paymentResult['status'] === 'succeeded') {
            $cartService->clearCart($user);
            $this->addFlash('success', 'Paiement accepté ! Votre commande est validée.');
        } elseif ($paymentResult['status'] === 'requires_action') {
            $this->addFlash('info', '3D Secure requis. Veuillez finaliser le paiement sur Stripe.');
        } else {
            $this->addFlash('error', 'Paiement échoué : ' . $paymentResult['message']);
        }

        return $this->redirectToRoute('app_cart');
    }
}
