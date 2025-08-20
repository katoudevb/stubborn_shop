<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    #[Route('/shop', name: 'app_shop')]
    public function index(ProductRepository $productRepository): Response
    {
        // Récupération de tous les produits
        $products = $productRepository->findAll();

        // Envoi de la variable 'products' à Twig
        return $this->render('shop/index.html.twig', [
            'title' => 'Boutique',
            'products' => $products,
        ]);
    }
}
