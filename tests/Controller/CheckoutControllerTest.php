<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    public function testCheckoutProcess(): void
    {
        $client = static::createClient();

        // 1️⃣ Ajouter un produit au panier via la route réelle
        $client->request('GET', '/cart/add/1');
        $this->assertResponseIsSuccessful();

        // Vérification du panier après ajout
        $session = $client->getRequest()->getSession();
        $cart = $session->get('cart', []);
        $this->assertNotEmpty($cart, 'Le panier doit contenir au moins un produit après ajout.');
        $this->assertEquals(1, $cart[0]['id']);
        $this->assertEquals(1, $cart[0]['quantity']);

        // 2️⃣ Accéder à la page checkout
        $client->request('GET', '/checkout');
        $this->assertResponseIsSuccessful();

        // 3️⃣ Confirmer l'achat
        $client->request('POST', '/checkout/confirm');
        $this->assertResponseRedirects('/order/confirmation');

        // 4️⃣ Suivre la redirection et vérifier le message de confirmation
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Merci pour votre achat');

        // 5️⃣ Vérifier que le panier est vidé après l'achat
        $this->assertEmpty($session->get('cart'), 'Le panier doit être vide après un achat.');
    }
}
