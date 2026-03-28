<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CategoryFactory::createSequence(
            json_decode(
                file_get_contents('data/Category.json', true), true
            )
        );
    }

}
