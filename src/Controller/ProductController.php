<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $minPrice = $request->query->get('min_price');
        $maxPrice = $request->query->get('max_price');

        $criteria = [];

        if ($minPrice !== null) {
            $criteria['minPrice'] = (float) $minPrice;
        }
        if ($maxPrice !== null) {
            $criteria['maxPrice'] = (float) $maxPrice;
        }

        // Méthode custom à créer dans ProductRepository pour filtre prix
        $products = $productRepository->findByPriceRange($criteria);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
