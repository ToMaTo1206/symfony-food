<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        UserFactory::createOne(
            ['email' => 'thomas@gmail.com',
                'password' => 'test',
                'firstName' => 'Thomas',
                'lastName' => 'Denoyelle']
        );
        UserFactory::createMany(20);
    }
}
