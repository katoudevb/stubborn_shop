<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;

class CartTest extends WebTestCase
{
    public function testAddProductToCart(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        // Récupération d’un produit existant
        $product = $em->getRepository(Product::class)->find(1);
        $this->assertNotNull($product, 'Le produit doit exister');

        // Vérifier qu’il y a au moins une taille disponible
        $availableSizes = $product->getAvailableSizes();
        $this->assertNotEmpty($availableSizes, 'Le produit doit avoir une taille disponible');

        // Choisir une taille pour le test
        $size = $availableSizes[0];

        // Aller sur la page du produit
        $crawler = $client->request('GET', '/product/' . $product->getId());
        $this->assertResponseIsSuccessful();

        // Ajouter le produit au panier
        $client->submitForm('Ajouter au panier', [
            'size' => $size,
            'quantity' => 1
        ]);

        $client->followRedirect();

        // Vérifier que le produit et la taille sont bien dans le panier
        $this->assertSelectorTextContains('.cart-item', $product->getName());
        $this->assertSelectorTextContains('.cart-item', $size);
    }
}
