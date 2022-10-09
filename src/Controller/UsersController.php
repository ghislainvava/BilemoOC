<?php

namespace App\Controller;

use OA\Parameter;
use App\Entity\Client;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class UsersController extends AbstractController
{
     #[Route('/api/users', name: 'users_list', methods: ['GET'])]
    // public function getAllUserClient( CustomerRepository $userRepo, SerializerInterface $serializer, Request $request): JsonResponse
    // {
    //     //$clientId = 16;
    //     $user = $this->getUser();

    //     $userClientList = $userRepo->findByClientId($user->getId());
    //     $page = $request->get('page', 1); //parametre par defaut
    //     $limit = $request->get('limit', 3);

    //     //$produitslist = $produitsRepo->findAll(); sans pagination
    //     //$produitslist = $produitsRepo->findAllWithPagination($page, $limit); sans cache

    //     $idCache = "getAllCustomers". $page. "-".$limit;

    //     $jsonUserClientList = $serializer->serialize($userClientList, 'json', ['groups' => 'getUsers'] );

    //     return new JsonResponse($jsonUserClientList, Response::HTTP_OK, [], true);
        
    // }
     public function getAllUserClient( CustomerRepository $userRepo, TagAwareCacheInterface $cache, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1); //parametre par defaut
        $limit = $request->get('limit', 3);
        $user = $this->getUser();
        $userClientList = $userRepo->findByClientId($user->getId());
    

        $idCache = "getAllUserClient". $page. "-".$limit;
        $userClientList = $cache->get($idCache, function(ItemInterface $item) use ($userRepo, $page, $limit){
            echo("pas de cache");
            $item->tag("usersCache");

            return $userRepo->findAllWithPagination($page, $limit);
        });

        $jsonUserClientList = $serializer->serialize($userClientList, 'json', ['groups' => 'getUsers'] );

        return new JsonResponse($jsonUserClientList, Response::HTTP_OK, [], true);
        
    }

    
    #[Route('/api/users/{id}', name: 'user_detail', methods: ['GET'])]
    public function getUserClient(int $id, CustomerRepository $userRepo): JsonResponse
    {
        $user = $this->getUser()->getId();
        //$customerId = 16;
        //$id = 78;
        $userClient = $userRepo->findCustomerById($user, $id);

        // $jsonUserClient = $serializer->serialize($userClient, 'json', ['groups' => 'getUsers'] );
        // return new JsonResponse($jsonUserClient, Response::HTTP_OK, [], true);
        //Alternative d'écriture
        $jsonResponse = $this->json($userClient[0], 200, [], ['groups' => 'getUsers']);
        return $jsonResponse;
        
    }
    #[Route('/api/users', name: 'app_create', methods: ['POST'])]
    public function addUserToClient( Request $request, SerializerInterface $serializer)
    {
        $json = $request->getContent();
        $post = $serializer->deserialize($json, Customer::class, 'json');

       dd($json);
    }

    #[Route('/api/users', name: 'app_user_deleteclient', methods: ['DELETE'])]
    public function deleteUserClient(CustomerRepository $userRepo, Customer $customer, EntityManagerInterface $em): JsonResponse
    {
     
        $em->remove($customer);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/users', name: 'app_create_user', methods: ['POST'])]
    public function addUserClient( Request $request, ClientRepository $clientrepo, EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
       $post = $request->getContent();
       $customer= $serializer->deserialize($post, Customer::class, 'json');
       $customer->setClient_id(5);
       //dd($toto, $customer);
       try{
            $errors = $validator->validate($customer);

            if (count($errors) > 0) {
               //return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
                return $this->json($errors); 
            }
            dd($customer);
            $em->persist($customer);
            $em->flush();
            // $jsonCustomer= $serializer->serialize($customer, 'json', ['groups' => 'getBooks']);
            // $location = $urlGenerator->generate('detailBook', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            // return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ["Location" => $location], true);
            return $this->json($customer, 201, [], ['groups' => 'getUsers']);
            } catch (NotEncodableValueException $e) {
                return $this->json([
                    'status => 400',
                    'message' => $e->getMessage()
                ], 400);
            }        
    }
}
