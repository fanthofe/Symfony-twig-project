<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i=1; $i<=7; $i++){
            $category = new Category();
            
            $category->setName($faker->jobTitle())
                ->setCreatedAt($faker->dateTime('now'))
                ->updatedTimestamps();
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1; // smaller means sooner
    }
}