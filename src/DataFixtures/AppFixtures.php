<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Client;
use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
     public function __construct( private readonly UserPasswordHasherInterface $encoder){

    }
    public function load(ObjectManager $manager): void
    {
     
        $faker = Factory::create('fr_FR');
    
        for ($nbclients = 1; $nbclients <= 30; $nbclients++) {
                $fakePassword = 'password';
                $client = new Client();
                if ($nbclients === 1) {
                $client->setRoles(['ROLE_ADMIN']);
                } else {
                    $client->setRoles(['ROLE_USER']);
                }
                    
                $client->setName($faker->company());
                $client->setPassword($this->encoder->hashPassword($client, $fakePassword ));
                $client->setEmail($faker->email());
                //$client->addUser($user);
                $manager->persist($client);

                for ($nbUsers = 1; $nbUsers <= 5; $nbUsers++) {
                    
                    $customer = new Customer();
                    $customer->setEmail($faker->email());
                    $customer->setClientId($client);
                    $manager->persist($customer);
                
                }
        
        }

        $manager->flush();
    // }
    // public function load(ObjectManager $manager): void
    // {
      //  $faker = Factory::create('fr_FR');
      
        for ($i = 1; $i <= 10; $i++) {
            $ram = array(4,8,16,32);
            $stockage = array(32, 64, 128, 256);
            $marque = array('Apple', 'Samsung', 'Xiaomi', 'Redmi', 'Oppo', 'Motorola');
            //$client = $this->getReference('client_'. $faker->numberBetween(1, 7));
            $produit = new Product();
            $produit->setModelName($faker->numerify('Model-####'));
            //$produit->setEcran($faker->randomfloat(2, 4, 7));
            $produit->setEcran($faker->numerify('#.#-Pouces'));
            $produit->setCapteurPhoto($faker->numerify('##-Pixels'));
            $produit->setCPU($faker->words(2, true));
            $produit->setRam($ram[array_rand($ram)]);
            $produit->setStockage($stockage[array_rand($stockage)]);
            $produit->setremarques($faker->Realtext(100));
            $produit->setMarque($marque[array_rand($marque)]);
            $produit->setprice($faker->randomFloat(2, 99, 1200));
            $manager->persist($produit);
        }

        $manager->flush();
    }
}
