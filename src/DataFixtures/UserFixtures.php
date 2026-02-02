<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\FoodFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $thomas = UserFactory::createOne([
            'email' => 'thomas@gmail.com',
            'password' => 'test', // Sera hashé grâce à ta config précédente
            'firstname' => 'Thomas',
            'lastname' => 'Denoyelle',
            // Pas besoin de définir 'aliments' ici si on veut faire du spécifique juste après
        ]);

        // On ajoute ton Steak manuellement à Thomas
        FoodFactory::createOne([
            'name' => 'Steak haché',
            'expiryDate' => new \DateTime('+6 day'),
            'user' => $thomas, // <--- C'est là que la magie opère : on lie l'aliment à Thomas
        ]);

        // On ajoute 10 autres trucs au pif dans ton frigo
        FoodFactory::createMany(10, [
            'user' => $thomas,
        ]);


        // 2. Création des 20 utilisateurs random, CHACUN avec son propre frigo rempli
        UserFactory::createMany(20, [
            // Pour chaque user créé, Foundry va appeler FoodFactory pour générer entre 5 et 10 aliments
            'food' => FoodFactory::new()->many(5, 10),
        ]);
    }
}
