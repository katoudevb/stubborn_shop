<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        // Récupérer les produits mis en avant (ex: avec un champ 'featured' = true)
        $featuredProducts = $productRepository->findBy(['featured' => true]);

        // Données statiques infos société (à adapter)
        $companyInfo = [
            'name' => 'Stubborn',
            'address' => 'Piccadilly Circus, London W1J 0DA, Royaume-Uni',
            'email' => 'stubborn@blabla.com',
            'slogan' => "Don't compromise on your look"
        ];

        return $this->render('home/index.html.twig', [
            'companyInfo' => $companyInfo,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
