<?php

namespace App\Service;

use App\Factory\StripePaymentIntentFactory;
use Stripe\PaymentIntent;

class StripeServices
{
    private StripePaymentIntentFactory $paymentIntentFactory;

    public function __construct(StripePaymentIntentFactory $paymentIntentFactory)
    {
        $this->paymentIntentFactory = $paymentIntentFactory;
    }

    public function createPaymentIntent(int $amount, string $currency = 'eur'): PaymentIntent
    {
        return $this->paymentIntentFactory->create([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method_types' => ['card'],
        ]);
    }
}
