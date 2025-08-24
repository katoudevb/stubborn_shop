<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;

class CheckoutTest extends WebTestCase
{
    public function testPurchase(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        // Récupérer un produit et une taille disponible
        $product = $em->getRepository(Product::class)->find(1);
        $this->assertNotNull($product);

        $availableSizes = $product->getAvailableSizes();
        $this->assertNotEmpty($availableSizes);

        $size = $availableSizes[0];

        // Ajouter le produit au panier
        $client->request('GET', '/product/' . $product->getId());
        $client->submitForm('Ajouter au panier', [
            'size' => $size,
            'quantity' => 1
        ]);
        $client->followRedirect();

        // Accéder à la page de checkout
        $crawler = $client->request('GET', '/checkout');

        // Soumettre le formulaire d’achat
        $client->submitForm('Valider la commande', [
            'checkout[cardNumber]' => '4242424242424242',
            'checkout[expiration]' => '12/30',
            'checkout[cvv]' => '123'
        ]);

        $client->followRedirect();

        // Vérifier la confirmation
        $this->assertSelectorTextContains('.confirmation', 'Merci pour votre achat');
    }
}
