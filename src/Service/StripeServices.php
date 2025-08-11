<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeServices
{
    private $secretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->secretKey = $stripeSecretKey;
        Stripe::setApiKey($this->secretKey);
    }

    public function createPaymentIntent(int $amount, string $currency = 'eur'): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method_types' => ['card'],
        ]);
    }
}
