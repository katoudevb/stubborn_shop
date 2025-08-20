<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ProductControllerTest extends WebTestCase
{
    public function testDeleteProduct(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // --- Étape 1 : Créer un produit en base ---
        $product = new Product();
        $product->setName('Sweat Test');
        $product->setPrice(49.99);
        $product->setFeatured(false);
        $entityManager->persist($product);
        $entityManager->flush();

        $productId = $product->getId();

        // --- Étape 2 : Charger la page détail produit ---
        $crawler = $client->request('GET', '/admin/product/' . $productId);
        $this->assertResponseIsSuccessful();

        // --- Étape 3 : Récupérer le formulaire de suppression ---
        $form = $crawler->selectButton('Supprimer')->form();

        // --- Étape 4 : Soumettre le formulaire (POST /admin/product/{id}/delete) ---
        $client->submit($form);

        // Vérifier la redirection après suppression
        $this->assertResponseRedirects('/admin/products'); 
        $client->followRedirect();

        // --- Étape 5 : Vérifier que le produit n’existe plus ---
        $deletedProduct = $entityManager->getRepository(Product::class)->find($productId);
        $this->assertNull($deletedProduct, 'Le produit devrait avoir été supprimé de la base.');
    }
}
