<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{
    #[Route('/api/produits', name: 'app_produits', methods: ['GET'])]
    public function getAllProduits(ProduitsRepository $produitsRepo, TagAwareCacheInterface $cache,SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1); //parametre par defaut
        $limit = $request->get('limit', 3);

        //$produitslist = $produitsRepo->findAll(); sans pagination
        //$produitslist = $produitsRepo->findAllWithPagination($page, $limit); sans cache

        $idcache = "getAllProduits". $page. "-".$limit;
        $produitsList = $cache->get($idcache, function(ItemInterface $item) use ($produitsRepo, $page, $limit){
            echo("pas de cache");
            $item->tag("produitsCache");
            
            return $produitsRepo->findAllWithPagination($page, $limit);
        });
          
        $jsonProduitsList = $serializer->serialize($produitsList, 'json' );

        return new JsonResponse($jsonProduitsList, Response::HTTP_OK, [], true);
        
    }

    #[Route('/api/produits/{id}', name: 'detail_Produit', methods: ['GET'])]
    public function getDetailProduit(Produits $produit, SerializerInterface $serializer): JsonResponse 
    {
        $jsonProduit = $serializer->serialize($produit, 'json');
        return new JsonResponse($jsonProduit, Response::HTTP_OK,  [], true);
    }
}