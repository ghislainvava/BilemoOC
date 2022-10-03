<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Produits;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
      
        for ($i = 1; $i <= 10; $i++) {
            $ram = array(4,8,16,32);
            $stockage = array(32, 64, 128, 256);
            $marque = array('Apple', 'Samsung', 'Xiaomi', 'Redmi', 'Oppo', 'Motorola');
            //$client = $this->getReference('client_'. $faker->numberBetween(1, 7));
            $produit = new Produits();
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
