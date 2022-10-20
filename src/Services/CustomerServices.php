<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CustomerRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CustomerServices 
{
    public function __construct( 
        private TagAwareCacheInterface $cache, 
        private EntityManagerInterface $em
       ){     
    }
    public function getAttributs(CustomerRepository $customerRepo, Request $request)
    {
        $page = $request->get('page', 1); //parametre par defaut
        $limit = $request->get('limit', 3);
        
        $idCache = "getAllUserClient". $page. "-".$limit;
        $customerInClient = $this->cache->get($idCache, function(ItemInterface $item) use ($customerRepo, $page, $limit){
            echo("pas de cache");
            $item->tag("usersCache");

            return $customerRepo->findAllWithPagination($page, $limit);
        });
        return $customerInClient;
    }

    public function eManager( $customer)
    {
        $this->em->persist($customer);
        $this->em->flush();
    }

       public function eRemoveManager( $customer)
    {
        $this->em->remove($customer);
        $this->em->flush();
    }
}
