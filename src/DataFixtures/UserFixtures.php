<?php

namespace App\DataFixtures;

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
            'password' => 'test',
            'firstname' => 'Thomas',
            'lastname' => 'Denoyelle',
        ]);

        FoodFactory::createOne([
            'name' => 'Steak haché',
            'expiryDate' => new \DateTime('+6 day'),
            'user' => $thomas,
        ]);

        FoodFactory::createMany(10, [
            'user' => $thomas,
        ]);

        UserFactory::createMany(20, [
            'food' => FoodFactory::new()->many(5, 10),
        ]);
    }
}
