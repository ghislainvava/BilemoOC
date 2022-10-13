<?php

namespace App\Controller;

use OA\Get;
use OA\Parameter;
use App\Entity\Produits;
use App\Services\PageService;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\Schema;
use App\Repository\ProduitsRepository;
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
     * @param ProduitsRepository $produitsRepo
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'products_list', methods: ['GET'])]
    public function getAllProducts(PageService $paginate, ProduitsRepository $produitsRepo, TagAwareCacheInterface $cache,SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1); 
        $limit = $request->get('limit', 3);
      
        $idCache = "getAllProducts". $page. "-".$limit;
        $produitsList = $cache->get($idCache, function(ItemInterface $item) use ($produitsRepo, $page, $limit){
            echo("pas de cache");
            $item->tag("productsCache");
            
            return $produitsRepo->findAllWithPagination($page, $limit);
        });
          
        $jsonProduitsList = $serializer->serialize($produitsList, 'json');

        return new JsonResponse($jsonProduitsList, Response::HTTP_OK, [], true);   
    }

    /**
     * @OA\Tag(name="Products")
     */
    #[Route('/api/product/{id}', name: 'Product_detail', methods: ['GET'])]
    public function getDetailProduct(Produits $produit, SerializerInterface $serializer): JsonResponse 
    {
        $jsonProduit = $serializer->serialize($produit, 'json');
        return new JsonResponse($jsonProduit, Response::HTTP_OK,  [], true);
    }
}