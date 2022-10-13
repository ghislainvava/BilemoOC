<?php

namespace App\Controller;

use OA\Get;
use OA\Parameter;
use App\Entity\Product;
use App\Services\PageService;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\Schema;
use App\Repository\ProductRepository;
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
    public function getAllProducts(PageService $paginate, ProductRepository $productRepo, TagAwareCacheInterface $cache,SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1); 
        $limit = $request->get('limit', 3);
      
        $idCache = "getAllProducts". $page. "-".$limit;
        $productList = $cache->get($idCache, function(ItemInterface $item) use ($productRepo, $page, $limit){
            echo("pas de cache");
            $item->tag("productsCache");
            
            return $productRepo->findAllWithPagination($page, $limit);
        });
          
        $jsonProductList = $serializer->serialize($productList, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);   
    }

    /**
     * @OA\Tag(name="Products")
     */
    #[Route('/api/product/{id}', name: 'Product_detail', methods: ['GET'])]
    public function getDetailProduct(Product $produit, SerializerInterface $serializer): JsonResponse 
    {
        $jsonProduit = $serializer->serialize($produit, 'json');
        return new JsonResponse($jsonProduit, Response::HTTP_OK,  [], true);
    }
}