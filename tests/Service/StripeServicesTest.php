<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\StripeServices;
use Stripe\PaymentIntent;

class StripeServicesTest extends TestCase
{
    public function testCreatePaymentIntent()
    {
        $mockPaymentIntent = $this->createMock(PaymentIntent::class);

        // Factory factice qui retourne le mock
        $factory = function (array $params) use ($mockPaymentIntent) {
            return $mockPaymentIntent;
        };

        $stripeService = new StripeServices($factory);

        $result = $stripeService->createPaymentIntent(1000);

        $this->assertSame($mockPaymentIntent, $result);
    }
}
