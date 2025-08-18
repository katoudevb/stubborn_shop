<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Retourne les produits filtrés par une fourchette de prix (ex: "10-29")
     */
    public function findByPriceRange(?string $priceRange)
    {
        $qb = $this->createQueryBuilder('p');

        if ($priceRange) {
            [$minPrice, $maxPrice] = explode('-', $priceRange);
            $qb->andWhere('p.price >= :minPrice')
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('minPrice', (float)$minPrice)
                ->setParameter('maxPrice', (float)$maxPrice);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Vérifie toutes les règles du devoir :
     * 1. Stock ≥ 2 pour chaque taille
     * 2. Prix cohérent (identique pour toutes les tailles)
     * 3. Retourne la liste des produits mis en avant
     *
     * @return array Tableau des messages d'erreur et produits mis en avant
     */
    public function validateProducts(): array
    {
        $products = $this->findAll();
        $errors = [];
        $featuredProducts = [];

        $sizes = ['S', 'M', 'L', 'XL'];

        foreach ($products as $product) {
            // Vérification stock
            $productSizes = [];
            foreach ($product->getStocks() as $stock) {
                $productSizes[$stock->getSize()] = $stock->getQuantity();
            }
            foreach ($sizes as $size) {
                if (!isset($productSizes[$size]) || $productSizes[$size] < 2) {
                    $errors[] = sprintf(
                        'Produit "%s" a un stock insuffisant pour la taille %s',
                        $product->getName(),
                        $size
                    );
                }
            }

            // Vérification mise en avant
            if ($product->isFeatured()) { // supposé champ boolean 'featured'
                $featuredProducts[] = $product->getName();
            }

            // Vérification prix (ici juste un rappel, le prix est unique dans Product)
            if ($product->getPrice() <= 0) {
                $errors[] = sprintf(
                    'Produit "%s" a un prix invalide',
                    $product->getName()
                );
            }
        }

        return [
            'errors' => $errors,
            'featured' => $featuredProducts
        ];
    }
}
