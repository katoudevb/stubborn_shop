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
    #[Route('/products', name: 'app_products')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $selectedRange = $request->query->get('price_range', null);
        $selectedRange = is_string($selectedRange) ? trim($selectedRange) : null;

        $products = $productRepository->findByPriceRange($selectedRange);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'selectedRange' => $selectedRange,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET', 'POST'])]
    public function show(int $id, ProductRepository $productRepository, Request $request): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $availableSizes = $product->getAvailableSizes();

        if ($request->isMethod('POST')) {
            $size = $request->request->get('size');

            if (!in_array($size, $availableSizes)) {
                $this->addFlash('error', 'Taille invalide.');
                return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
            }

            $totalStock = $product->getStockBySize($size);
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

    #[Route('/admin/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $product = new Product();

        // Pré-remplir les tailles dans le formulaire
        $sizes = ['XS','S','M','L','XL'];
        foreach ($sizes as $size) {
            $stock = new \App\Entity\Stock();
            $stock->setSize($size);
            $stock->setQuantity(0);
            $product->addStock($stock);
        }

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

    #[Route('/admin/product/{id}/edit', name: 'app_product_edit')]
public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                return $this->redirectToRoute('app_product_edit', ['id' => $product->getId()]);
            }

            $product->setImage($newFilename);
        }

        $em->flush(); // PAS besoin de persist ici, le produit existe déjà

        $this->addFlash('success', 'Produit modifié avec succès.');
        return $this->redirectToRoute('app_products');
    }

    return $this->render('product/edit.html.twig', [
        'form' => $form->createView(),
        'product' => $product,
    ]);
}

    #[Route('/admin/product/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $em): Response
    {
    // Vérifie le CSRF token
    $submittedToken = $request->request->get('_token');
    if ($this->isCsrfTokenValid('delete'.$product->getId(), $submittedToken)) {
        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'Produit supprimé avec succès.');
    } else {
        $this->addFlash('error', 'Token CSRF invalide. Suppression annulée.');
    }

    return $this->redirectToRoute('app_products');
}
};
