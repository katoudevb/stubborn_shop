<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class CheckProductsController extends AbstractController
{
    #[Route('/check-products', name: 'check_products')]
    public function check(ProductRepository $productRepository): Response
    {
        $result = $productRepository->validateProducts();

        $output = '';

        if (!empty($result['errors'])) {
            $output .= "<h2>Erreurs trouvées :</h2><ul>";
            foreach ($result['errors'] as $error) {
                $output .= "<li>$error</li>";
            }
            $output .= "</ul>";
        } else {
            $output .= "<p>Tous les produits respectent les règles de stock et prix.</p>";
        }

        $output .= "<h2>Produits mis en avant :</h2><ul>";
        foreach ($result['featured'] as $productName) {
            $output .= "<li>$productName</li>";
        }
        $output .= "</ul>";

        return new Response($output);
    }
}
