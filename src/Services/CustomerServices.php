<?php

namespace App\Services;

use App\Entity\Client;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CustomerServices 
{
    public function __construct( 
        private TagAwareCacheInterface $cache, 
        private EntityManagerInterface $em,
        private CustomerRepository $customerRepo
       ){     
    }
    public function getAttributs(Client $client, Request $request)
    {
        $page = $request->get('page', 1); //parametre par defaut
        $limit = $request->get('limit', 3);
        $idCache = "getAllUserClient". $client->getId(). "-". $page. "-".$limit;
        $customerInClient = $this->cache->get($idCache, function(ItemInterface $item) use ( $client, $page, $limit){
            echo("pas de cache");
            $item->tag("usersCache");

            return $this->customerRepo->findAllWithPagination($client, $page, $limit);
        });
        return $customerInClient;
    }

    public function eManager( $customer)
    {

        $this->em->persist($customer);
        $this->em->flush();
        $this->deletecache();
    }

       public function eRemoveManager( $customer)
    {
        $this->em->remove($customer);
        $this->em->flush();
        $this->deletecache();
    }

    public function findCustomerById($clientId, $id)
    {
        return $this->customerRepo->findCustomerById($clientId, $id);
    }
    
    public function deletecache()
    {
        $this->cache->invalidateTags(['usersCache']);

    }
}
