<?php

namespace App\Tests\Service;

use App\Service\StripeService;
use App\Factory\StripePaymentIntentFactory;
use PHPUnit\Framework\TestCase;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeServiceTest extends TestCase
{
    public function testSimulatePaymentReturnsArray()
    {
        // Création d'un mock du PaymentIntent
        $mockPaymentIntent = $this->createMock(PaymentIntent::class);
        $mockPaymentIntent->status = 'succeeded';
        $mockPaymentIntent->client_secret = 'secret_test';

        // Création d'un mock de la factory
        $mockFactory = $this->createMock(StripePaymentIntentFactory::class);
        $mockFactory->method('create')->willReturn($mockPaymentIntent);

        $service = new StripeService($mockFactory);

        $result = $service->simulatePayment(1000);

        $this->assertIsArray($result);
        $this->assertEquals('succeeded', $result['status']);
        $this->assertEquals('Paiement accepté', $result['message']);
        $this->assertEquals('secret_test', $result['client_secret']);
    }
}
