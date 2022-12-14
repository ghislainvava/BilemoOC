<?php

namespace App\Controller;

use App\Entity\Customer;
use OpenApi\Annotations as OA;
use App\Services\CustomerServices;
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
     * @OA\Response(response="201", description="Success")
     * @OA\Response(response="401", description="Non authorisé")
     * @OA\Response(response="400", description="Il y a une erreur dans la pagination")
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
     public function getAllUserClient( SerializerInterface $serializer, Request $request, CustomerServices $customerServices): JsonResponse
    {
        $client = $this->getUser(); 
        $page = (int)$request->query->get('page');
        $limit = (int)$request->query->get('limit');
      
        if ($page == 0 || $limit == 0){  //vérification de page et limit
            return $this->json([
                    'status => 400',
                    'message' => 'Il y a une erreur dans la pagination'
                ], 400);
        }
        
        $customersInClient = $customerServices->getAttributs($client, $request);
       
         if (empty($customersInClient)){
            return new JsonResponse(status:Response::HTTP_NOT_FOUND);
        }
        $jsonUserClientList = $serializer->serialize($customersInClient, 'json', ['groups' => 'getCustomers'] );

        return new JsonResponse($jsonUserClientList, Response::HTTP_OK, [], true);    
    }

    /**
     * @OA\Tag(name="Customers")
     *   @OA\RequestBody(
     *     @OA\MediaType(mediaType="application/json")
     * )
     * @OA\Response(response="201", description="Success")
     * @OA\Response(response="401", description="Not authorized")
     * @OA\Response(response="400", description="Not right format")
     */
    #[Route('/api/customers/{id}', name: 'customer_detail', methods: ['GET'])]
    public function getDetailCustomer(SerializerInterface $serializer, int $id, CustomerServices $customerServices): JsonResponse
    {
         $client = $this->getUser();
         $customer = $customerServices->findCustomerById($client, $id);
         if(empty($customer)){
            return new JsonResponse(status:Response::HTTP_NOT_FOUND);
         }
   
        $jsonCustomer = $serializer->serialize($customer[0], 'json', ['groups' => 'getCustomers']);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, json:true);    
    }

    /**
     * @OA\Tag(name="Customers")
     * @OA\Response(response="201", description="Success")
     * @OA\Response(response="401", description="Not authorized")
     * @OA\Response(response="400", description="Not right format")
     */

    #[Route('/api/customers/{id}', name: 'customer_delete', methods: ['DELETE'])]
    public function deleteUserClient(CustomerServices $customerServices, int $id, ): JsonResponse
    {
        
        $client = $this->getUser();
        $customer = $customerServices->findCustomerById($client, $id);
        if(empty($customer)){
            return new JsonResponse(status:Response::HTTP_NOT_FOUND);
         }
        $customerServices->eRemoveManager($customer[0]);   
        return new JsonResponse(null, Response::HTTP_NO_CONTENT); //null corespond au parametre renvoyé
    }
    /**
     * @OA\Tag(name="Customers")
     * 
     * @OA\RequestBody(
     *     description="Login credentials",
     *     required=true,
     *     @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  example="ghis@free.fr"
     *              )
     *          )
     *      )
     * )
     * 
     *
     * @OA\Response(response="201", description="Success")
     * @OA\Response(response="401", description="Not authorized")
     * @OA\Response(response="403", description="Access denied")
     * @OA\Response(response="400", description="Not right format")
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $passwordHasher
     * @param ValidatorInterface $validator
     * @param CacheInterface $userPool
     * @return JsonResponse
     */
    #[Route('/api/customers', name: 'create_customer', methods: ['POST'])]
    public function addUserClient( Request $request, CustomerServices $customerServices,  SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
       $client_id = $this->getUser();
       $post = $request->getContent();
       $customer= $serializer->deserialize($post, Customer::class, 'json');
       $customer->setClient($client_id);
      
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
