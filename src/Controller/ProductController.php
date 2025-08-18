<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProductController extends AbstractController
{
    /**
     * Page liste des produits avec filtrage dynamique par prix
     */
    #[Route('/products', name: 'app_products')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $minPrice = $request->query->get('price_range_min');
        $maxPrice = $request->query->get('price_range_max');
        $validRange = null;

        if ($minPrice !== null && $maxPrice !== null) {
            $min = (float) $minPrice;
            $max = (float) $maxPrice;
            if ($min < $max) {
                $validRange = "$min-$max";
            }
        }

        $products = $productRepository->findByPriceRange($validRange);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'selectedRange' => $validRange,
        ]);
    }

    /**
     * Page détail produit et ajout au panier avec gestion du stock et des tailles
     */
    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET', 'POST'])]
    public function show(Product $product, Request $request): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $availableSizes = $product->getAvailableSizes(); // à créer dans Product

        if ($request->isMethod('POST')) {
            $size = $request->request->get('size');

            if (!in_array($size, $availableSizes)) {
                $this->addFlash('error', 'Taille invalide.');
                return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
            }

            $totalStock = $product->getStockBySize($size); // à créer dans Product
            $currentQuantityInCart = $cart[$product->getId()][$size] ?? 0;
            $stockRemaining = $totalStock - $currentQuantityInCart;

            if ($stockRemaining > 0) {
                $cart[$product->getId()][$size] = $currentQuantityInCart + 1;
                $session->set('cart', $cart);
                $this->addFlash('success', 'Produit ajouté au panier !');
            } else {
                $this->addFlash('error', 'Stock insuffisant pour cette taille.');
            }

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'availableSizes' => $availableSizes,
        ]);
    }

    /**
     * Création d'un nouveau produit (admin)
     */
    #[Route('/admin/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                    return $this->redirectToRoute('app_product_new');
                }

                $product->setImage($newFilename);
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
