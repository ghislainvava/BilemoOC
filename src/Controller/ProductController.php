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
     *     response=201,description="Sucess",
     *     response="401", description="Not authorized",
     *      response="400", description="Not right format",
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
        $page = (int)$request->query->get('page');
        $limit = (int)$request->query->get('limit');
      
        if ($page == 0 || $limit == 0){   //vérification de page et limit
            return $this->json([
                    'status => 400',
                    'message' => 'Il y a une erreur dans la pagination'
                ], 400);
        }
        $productsList = $productsServices->getAttributs( $request);
         if (empty($productsList)){
            return new JsonResponse(status:Response::HTTP_BAD_REQUEST);
        }
        $jsonProductList = $serializer->serialize($productsList, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);   
    }

    /**
     * @OA\Tag(name="Products")
     * @OA\Response(response="201", description="Success")
     * @OA\Response(response="401", description="Not authorized")
     * @OA\Response(response="400", description="Not right format")
     */
    #[Route('/api/products/{id}', name: 'Product_detail', methods: ['GET'])]
    public function getDetailProduct(Product $produit, SerializerInterface $serializer): JsonResponse 
    {
        $jsonProduit = $serializer->serialize($produit, 'json'); //doctrine
         if (empty($jsonProduit)){
            return new JsonResponse(status:Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse($jsonProduit, Response::HTTP_OK,  [], true);
    }
}