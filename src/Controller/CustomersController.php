<?php

namespace App\Controller;

use App\Entity\Customer;
use OpenApi\Annotations as OA;
use App\Services\CustomerServices;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
     public function getAllUserClient( CustomerRepository $customerRepo, SerializerInterface $serializer, Request $request, CustomerServices $customerServices): JsonResponse
    {
       
        $customersInClient = $customerServices->getAttributs($customerRepo,$request);
        $jsonUserClientList = $serializer->serialize($customersInClient, 'json', ['groups' => 'getCustomers'] );

        return new JsonResponse($jsonUserClientList, Response::HTTP_OK, [], true);
        
    }

    /**
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customers/{id}', name: 'customer_detail', methods: ['GET'])]
    public function getDetailCustomer(SerializerInterface $serializer, Customer $customer, int $id, CustomerRepository $userRepo): JsonResponse
    {
         $client = $this->getUser();
         $customer = $userRepo->findCustomerById($client, $id);;
       
        // $jsonResponse = $this->json($customer[0], 200, [], ['groups' => 'getUsers']);
        // return $jsonResponse;
        $jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getCustomer']);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK,  [], true);    
    }

    /**
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customers', name: 'customer_delete', methods: ['DELETE'])]
    public function deleteUserClient(Customer $customer, CustomerServices $customerServices): JsonResponse
    {
        $customerServices->eRemoveManager($customer);   
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customer', name: 'create_customer', methods: ['POST'])]
    public function addUserClient( Request $request, CustomerServices $customerServices,  SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
       $client_id = $this->getUser();
       $post = $request->getContent();
       $customer= $serializer->deserialize($post, Customer::class, 'json');
       $customer->setClient_id($client_id);
      
       try{
            $errors = $validator->validate($customer);

            if (count($errors) > 0) {
         
                throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "La requête est invalide");
            }

            $customerServices->eManager($customer);   
            return $this->json($customer, 201, [], ['groups' => 'getCustomers']);

            } catch (NotEncodableValueException $e) {
                return $this->json([
                    'status => 400',
                    'message' => $e->getMessage()
                ], 400);
            }        
    }
}
