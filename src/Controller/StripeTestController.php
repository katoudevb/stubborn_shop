<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StripeService;

class StripeTestController extends AbstractController
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    #[Route('/stripe/test-payment', name: 'stripe_test_payment')]
    public function testPayment(Request $request): JsonResponse
    {
        $amount = (int) $request->query->get('amount', 5000); // 50€ par défaut
        $card = $request->query->get('card'); // numéro de test

        $result = $this->stripeService->simulatePayment($amount, 'eur', $card);

        return $this->json($result);
    }
}
