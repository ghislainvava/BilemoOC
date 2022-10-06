<?php

namespace App\Controller;

use OA\Parameter;
use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\Schema;

class ProductController extends AbstractController
{
    
   
    //  * @OA\Response(
    //  *     response=200,
    //  *     description="Retourne la liste des téléphones",
    //  * @OA\MediaType(
    //  * mediaType="application/json",
    //  * )
    //  * ),
    //  * )
    //  * @OAParameter(
    //  *     name="page",
    //  *     in="query",
    //  *     description="La page que l'on veut récupérer",
    //  *     @OA\Schema(type="int")
    //  * )
    //  *
    //  * @OA\Parameter(
    //  *     name="limit",
    //  *     in="query",
    //  *     description="Le nombre d'éléments que l'on veut récupérer",
    //  *     @OA\Schema(type="int")
    //  * )
    //  
     /**
     * @OA\Tag(name="Produits")
     * @param ProduitsRepository $produitsRepo
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/produits', name: 'app_produits', methods: ['GET'])]
    public function getAllProduits(ProduitsRepository $produitsRepo, TagAwareCacheInterface $cache,SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1); //parametre par defaut
        $limit = $request->get('limit', 3);

        //$produitslist = $produitsRepo->findAll(); sans pagination
        //$produitslist = $produitsRepo->findAllWithPagination($page, $limit); sans cache

        $idCache = "getAllProduits". $page. "-".$limit;
        $produitsList = $cache->get($idCache, function(ItemInterface $item) use ($produitsRepo, $page, $limit){
            echo("pas de cache");
            $item->tag("produitsCache");
            
            return $produitsRepo->findAllWithPagination($page, $limit);
        });
          
        $jsonProduitsList = $serializer->serialize($produitsList, 'json');

        return new JsonResponse($jsonProduitsList, Response::HTTP_OK, [], true);
        
    }

    #[Route('/api/produits/{id}', name: 'detail_Produit', methods: ['GET'])]
    public function getDetailProduit(Produits $produit, SerializerInterface $serializer): JsonResponse 
    {
        $jsonProduit = $serializer->serialize($produit, 'json');
        return new JsonResponse($jsonProduit, Response::HTTP_OK,  [], true);
    }
}