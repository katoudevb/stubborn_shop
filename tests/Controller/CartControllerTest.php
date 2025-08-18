<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testAddProductToCart(): void
    {
        $client = static::createClient();

        // On appelle la route d'ajout au panier
        $client->request('GET', '/cart/add/1');

        // Vérifie que la réponse est OK
        $this->assertResponseIsSuccessful();

        // Vérifie que la session contient bien l'article ajouté
        $session = $client->getRequest()->getSession();
        $cart = $session->get('cart', []);

        $this->assertNotEmpty($cart, 'Le panier ne doit pas être vide après ajout');
        $this->assertEquals(1, $cart[0]['id']);
        $this->assertEquals(1, $cart[0]['quantity']);
    }
}
