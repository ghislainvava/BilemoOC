<?php

namespace App\Controller;

use OA\Get;
use OA\Parameter;
use App\Entity\Product;
use App\Services\PageService;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\Schema;
use App\Repository\ProductRepository;
use App\Services\ProductsServices;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    //  
    //  
     /**
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des téléphones",
     * @OA\MediaType(
     * mediaType="application/json",
     * )
     * ),
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     * 
     * @param ProductRepository $productRepo
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'products_list', methods: ['GET'])]
    public function getAllProducts( ProductsServices $productsServices, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $productsList = $productsServices->getAttributs( $request);
        $jsonProductList = $serializer->serialize($productsList, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);   
    }

    /**
     * @OA\Tag(name="Products")
     */
    #[Route('/api/products/{id}', name: 'Product_detail', methods: ['GET'])]
    public function getDetailProduct(Product $produit, SerializerInterface $serializer): JsonResponse 
    {
        $jsonProduit = $serializer->serialize($produit, 'json'); //doctrine
        return new JsonResponse($jsonProduit, Response::HTTP_OK,  [], true);
    }
}