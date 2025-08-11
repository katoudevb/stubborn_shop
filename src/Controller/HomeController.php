<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        // Récupérer les produits mis en avant (ex: avec un champ 'featured' = true)
        $featuredProducts = $productRepository->findBy(['featured' => true]);

        // Données statiques infos société (à adapter)
        $companyInfo = [
            'name' => 'Nom de la Société',
            'address' => '123 rue Exemple, Ville',
            'phone' => '01 23 45 67 89',
            'email' => 'contact@societe.fr',
        ];

        return $this->render('home/index.html.twig', [
            'companyInfo' => $companyInfo,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
