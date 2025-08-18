<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\ProductRepository;

class ProductRepositoryTest extends KernelTestCase
{
    private ?ProductRepository $productRepository = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->productRepository = self::getContainer()->get(ProductRepository::class);
    }

    public function testValidateProducts(): void
    {
        $result = $this->productRepository->validateProducts();

        // Vérifie qu'il n'y a pas d'erreurs
        $this->assertEmpty(
            $result['errors'],
            "Certaines règles ne sont pas respectées :\n" . implode("\n", $result['errors'])
        );

        // Vérifie qu'il y a au moins un produit mis en avant
        $this->assertNotEmpty(
            $result['featured'],
            "Aucun produit mis en avant n'a été trouvé."
        );

        // Affiche les produits mis en avant pour info
        echo "Produits mis en avant : " . implode(', ', $result['featured']) . "\n";
    }
}
