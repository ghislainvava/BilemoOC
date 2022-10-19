<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductsServices
{
     public function __construct( 
        private TagAwareCacheInterface $cache, 
        private EntityManagerInterface $em
       ){     
    }
    public function getAttributs(ProductRepository $productRepo, Request $request)
    {
        $page = $request->get('page', 1); //parametre par defaut
        $limit = $request->get('limit', 3);
        
        $idCache = "getAllProducts". $page. "-".$limit;
        $productList = $this->cache->get($idCache, function(ItemInterface $item) use ($productRepo, $page, $limit){
            echo("pas de cache");
            $item->tag("productsCache");

            return $productRepo->findAllWithPagination($page, $limit);
        });
        return $productList;
    }

}