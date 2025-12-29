<?php

namespace App\Controller\Api;

use Dom\Entity;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{   
    // #[Route('/api/products', name: 'app_api_products_index', methods: ['GET'])]
    #[Route('/api/products', name: 'app_api_product', methods: ['GET'])]
    public function index(ProductRepository $productRepository)

    {
        return $this->json($productRepository->findAll());    
    }

    // # [Route('/api/product', name: 'app_api_product_create', methods: ['POST'])]
    #[Route('/api/product', name: 'app_api_products_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setCreatedAt(new \DateTime());


        $em->persist($product);
        $em->flush();

        return $this->json($product, 201);
    }

    # Update Product
    #[Route('/api/product/{id}', name: 'app_api_product_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        $product->setName($data['name'] ?? $product->getName());
        $product->setPrice($data['price'] ?? $product->getPrice());
        $em->flush();
        return $this->json($product);
    }
    // Delete Product
    #[Route('/api/product/{id}', name: 'app_api_product_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }
        $em->remove($product);
        $em->flush();
        return $this->json(['message' => 'Product deleted']);
    }
}