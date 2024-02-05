<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i=1; $i <= 50; $i++){
            $client = new Client();
            $client->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->email)
                ->setEnterprise($faker->company())
                ->setPhone($faker->numerify('07##########'))
                ->setDateEntry($faker->dateTime('now'))
                ->setCountry($faker->country())
                ->setJob($faker->jobTitle())
                ->setCreatedAt($faker->dateTime('now'))
                ->updatedTimestamps();

            $manager->persist($client);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ArticleFixtures::class,
            CategoryFixtures::class,
            ArticleCategoryFixtures::class,
            DateEventFixtures::class,
            EnterpriseProjectsFixtures::class,
            UserDateEventFixtures::class
        ];
    }
}
