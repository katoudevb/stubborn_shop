<?php

namespace App\Factory;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentIntentFactory
{
    private string $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
        Stripe::setApiKey($this->stripeSecretKey);
    }

    /**
     * Crée un PaymentIntent Stripe
     *
     * @param int $amount Montant en centimes
     * @param string $currency Devise (ex: 'eur')
     * @return PaymentIntent
     */
    public function create(int $amount, string $currency = 'eur'): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method_types' => ['card'],
        ]);
    }
}
