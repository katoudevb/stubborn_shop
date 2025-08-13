<?php

namespace App\Tests\Factory;

use App\Factory\StripePaymentIntentFactory;
use PHPUnit\Framework\TestCase;
use Stripe\PaymentIntent;
use phpmock\phpunit\PHPMock;

class StripePaymentIntentFactoryTest extends TestCase
{
    use PHPMock;

    public function testCreateReturnsPaymentIntent()
    {
        // Créer un faux PaymentIntent à retourner par le mock
        $fakePaymentIntent = $this->createMock(PaymentIntent::class);

        // Mock de la méthode statique create dans le namespace Stripe
        $paymentIntentCreateMock = $this->getStaticMethodMock(
            'Stripe',       // Namespace où se trouve PaymentIntent
            'PaymentIntent::create'
        );

        // Configurer le mock pour retourner notre faux PaymentIntent
        $paymentIntentCreateMock->expects($this->once())
            ->with($this->arrayHasKey('amount'))  // on vérifie que les paramètres contiennent 'amount'
            ->willReturn($fakePaymentIntent);

        // On active le mock
        $paymentIntentCreateMock->enable();

        // Création de la fabrique avec une clé d’API fictive
        $factory = new StripePaymentIntentFactory('sk_test_fake');

        // Appel à create, qui déclenche le mock au lieu d’un appel réel
        $result = $factory->create([
            'amount' => 1000,
            'currency' => 'eur',
            'payment_method_types' => ['card'],
        ]);

        // Vérifie que le résultat est bien notre mock PaymentIntent
        $this->assertSame($fakePaymentIntent, $result);

        // Désactive le mock pour ne pas perturber d'autres tests
        $paymentIntentCreateMock->disable();
    }
}
