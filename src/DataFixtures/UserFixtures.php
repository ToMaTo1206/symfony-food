<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\FoodFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        UserFactory::createOne([
            'email' => 'thomas@gmail.com',
            'password' => 'test',
            'firstname' => 'Thomas',
            'lastname' => 'Denoyelle',
        ]);

        UserFactory::createMany(20);

        FoodFactory::createMany(100, function () {
            return [
                'user' => UserFactory::random(),
                'category' => CategoryFactory::random(),
            ];
        });
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
