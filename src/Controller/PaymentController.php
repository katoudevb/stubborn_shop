<?php

namespace App\Controller;

use App\Service\StripeServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function checkout(StripeServices $stripeServices): Response
    {
        // Montant en centimes (ex: 20€ = 2000)
        $paymentIntent = $stripeServices->createPaymentIntent(2000);

        return $this->render('checkout.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'stripePublicKey' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }

    #[Route('/confirmation', name: 'confirmation')]
    public function confirmation(): Response
    {
        return $this->render('payment/confirmation.html.twig');
    }
}
