<?php

namespace App\Service;

use App\Factory\StripePaymentIntentFactory;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    private StripePaymentIntentFactory $paymentIntentFactory;

    public function __construct(StripePaymentIntentFactory $paymentIntentFactory)
    {
        $this->paymentIntentFactory = $paymentIntentFactory;
    }

    /**
     * Simule le paiement d’une commande et retourne un message clair
     *
     * @param int $amount Montant en centimes
     * @param string $currency Devise
     * @param string|null $testCard Numéro de carte de test Stripe
     * @return array ['status' => string, 'message' => string, 'client_secret' => string|null]
     */
    public function simulatePayment(int $amount, string $currency = 'eur', ?string $testCard = null): array
    {
        try {
            $paymentIntent = $this->paymentIntentFactory->create($amount, $currency);

            if ($testCard) {
                $paymentIntent->confirm([
                    'payment_method_data' => [
                        'type' => 'card',
                        'card' => [
                            'number' => $testCard,
                            'exp_month' => 12,
                            'exp_year' => date('Y') + 1,
                            'cvc' => '123',
                        ],
                    ],
                ]);
            }

            // Déterminer le message en fonction du status Stripe
            switch ($paymentIntent->status) {
                case 'succeeded':
                    $message = 'Paiement accepté';
                    break;
                case 'requires_action':
                    $message = '3D Secure requis';
                    break;
                case 'requires_payment_method':
                case 'canceled':
                    $message = 'Paiement échoué';
                    break;
                default:
                    $message = 'Statut inconnu : ' . $paymentIntent->status;
            }

            return [
                'status' => $paymentIntent->status,
                'message' => $message,
                'client_secret' => $paymentIntent->client_secret ?? null,
            ];
        } catch (ApiErrorException $e) {
            // Gestion des erreurs Stripe
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'client_secret' => null,
            ];
        }
    }
}
