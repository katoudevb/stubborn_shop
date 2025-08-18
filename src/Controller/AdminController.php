<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        // Création des formulaires inline pour chaque produit
        $forms = [];
        foreach ($products as $product) {
            $forms[$product->getId()] = $this->createForm(ProductType::class, $product, [
                'action' => $this->generateUrl('admin_product_edit', ['id' => $product->getId()]),
                'method' => 'POST',
            ])->handleRequest($request);

            if ($forms[$product->getId()]->isSubmitted() && $forms[$product->getId()]->isValid()) {
                $em->flush();
                $this->addFlash('success', "Produit {$product->getName()} mis à jour !");
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        return $this->render('admin/index.html.twig', [
            'products' => $products,
            'forms' => $forms,
        ]);
    }

    #[Route('/new', name: 'admin_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', "Produit {$product->getName()} ajouté !");
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_product_edit', methods: ['GET', 'POST'])]
    public function edit(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', "Produit {$product->getName()} mis à jour !");
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function delete(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', "Produit {$product->getName()} supprimé !");
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
