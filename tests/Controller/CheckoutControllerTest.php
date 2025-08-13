<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\StripeServices;
use Stripe\PaymentIntent;

class CheckoutControllerTest extends WebTestCase
{
    public function testCheckoutProcess()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        // 1. Faire une requête pour initier la session
        $client->request('GET', '/');

        // 2. Récupérer la session
        $session = $client->getRequest()->getSession();

        // 3. Préparer le panier dans la session
        $session->set('cart', [
            ['id' => 1, 'quantity' => 2, 'price' => 10.0],
        ]);
        $session->save();

        // 4. Accéder à la page checkout
        $crawler = $client->request('GET', '/checkout');
        $this->assertResponseIsSuccessful();

        // 5. Simuler la confirmation du paiement
        $client->request('POST', '/checkout/confirm');
        $this->assertResponseRedirects('/order/confirmation');

        // 6. Suivre la redirection et vérifier le message
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Merci pour votre achat');

        // 7. Vérifier que le panier est vidé après l'achat
        $this->assertEmpty($session->get('cart'));
    }
}
