<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        // Récupération des 3 produits mis en avant
        $featuredProducts = $productRepository->findBy(['featured' => true], null, 3);

        // Debug temporaire pour vérifier que les produits sont récupérés
        // dump($featuredProducts);

        // Données statiques de l'entreprise
        $companyInfo = [
            'name' => 'Stubborn',
            'address' => 'Piccadilly Circus, London W1J 0DA, Royaume-Uni',
            'email' => 'stubborn@blabla.com',
            'slogan' => "Don't compromise on your look"
        ];

        // Pour test rapide : si aucun produit, créer des placeholders
        if (empty($featuredProducts)) {
            $featuredProducts = [
                (object)[
                    'id' => 1,
                    'name' => 'Blackbelt',
                    'price' => 29.99,
                    'imageFilename' => '1.jpeg'
                ],
                (object)[
                    'id' => 2,
                    'name' => 'Pokeball',
                    'price' => 45.00,
                    'imageFilename' => '4.jpeg'
                ],
                (object)[
                    'id' => 3,
                    'name' => 'BornInUsa',
                    'price' => 59.90,
                    'imageFilename' => '9.jpeg'
                ]
            ];
        }

        return $this->render('home/index.html.twig', [
            'companyInfo' => $companyInfo,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
