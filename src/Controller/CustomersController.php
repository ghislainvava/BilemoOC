<?php

namespace App\Controller;

use App\Entity\Customer;
use OpenApi\Annotations as OA;
use App\Repository\ClientRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class CustomersController extends AbstractController
{
      
    //  
     /**
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateurs du client",
     * @OA\MediaType(
     * mediaType="application/json",
     * )
     * ),
     *
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
     * @OA\Tag(name="Customers")
     * 
     * @param ProduitsRepository $produitsRepo
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
     #[Route('/api/customers', name: 'customers_list', methods: ['GET'])]

     public function getAllUserClient( CustomerRepository $customerRepo, TagAwareCacheInterface $cache, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1); //parametre par defaut
        $limit = $request->get('limit', 3);
        $client = $this->getUser();
        $customersInClient = $customerRepo->findByClientId($client->getId());
    

        $idCache = "getAllUserClient". $page. "-".$limit;
        $customersInClient = $cache->get($idCache, function(ItemInterface $item) use ($customerRepo, $page, $limit){
            echo("pas de cache");
            $item->tag("usersCache");

            return $customerRepo->findAllWithPagination($page, $limit);
        });

        $jsonUserClientList = $serializer->serialize($customersInClient, 'json', ['groups' => 'getUsers'] );

        return new JsonResponse($jsonUserClientList, Response::HTTP_OK, [], true);
        
    }
    /**
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customer/{id}', name: 'customer_detail', methods: ['GET'])]
    public function getUserClient(int $id, CustomerRepository $userRepo): JsonResponse
    {
        $client = $this->getUser()->getId();
        $customer = $userRepo->findCustomerById($client, $id);

        $jsonResponse = $this->json($customer[0], 200, [], ['groups' => 'getUsers']);
        return $jsonResponse;
        
    }

    /**
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customer', name: 'customer_delete', methods: ['DELETE'])]
    public function deleteUserClient(CustomerRepository $userRepo, Customer $customer, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($customer);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customer', name: 'create_customer', methods: ['POST'])]
    public function addUserClient( Request $request, ClientRepository $clientrepo, EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
       $client_id = $this->getUser();
       $post = $request->getContent();
       $customer= $serializer->deserialize($post, Customer::class, 'json');
       $customer->setClient_id($client_id);
      
       try{
            $errors = $validator->validate($customer);

            if (count($errors) > 0) {
               //return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
                //return $this->json($errors); 
                throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "La requête est invalide");
            }
            
            $em->persist($customer);
            $em->flush();
            // $jsonCustomer= $serializer->serialize($customer, 'json', ['groups' => 'getBooks']);
            // $location = $urlGenerator->generate('detailBook', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            // return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ["Location" => $location], true);
            return $this->json($customer, 201, [], ['groups' => 'getCustomers']);
            } catch (NotEncodableValueException $e) {
                return $this->json([
                    'status => 400',
                    'message' => $e->getMessage()
                ], 400);
            }        
    }
}
