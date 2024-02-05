<?php

namespace App\DataFixtures;

use App\Entity\Enterprise;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EnterpriseProjectsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i=1; $i<=50; $i++){
            $enterprise = new Enterprise();
            $enterprise->setName($faker->company())
                ->setExpertiseField($faker->word())
                ->setAddress($faker->streetAddress())
                ->setCity($faker->city())
                ->setCountry($faker->country())
                ->setPhoneNumber($faker->numerify('07##########'))
                ->setSiret($faker->numerify('5########'))
                ->setCreationDate($faker->dateTime('now'))
                ->setNumberDirector($faker->numberBetween($min=1, $max=5))
                ->setCreatedAt($faker->dateTime('now'))
                ->updatedTimestamps();
            
            $manager->persist($enterprise);

            $project = new Project();
            $project->setName($faker->word())
                ->setRessource($faker->numberBetween($min=3, $max=20))
                ->setEstimationDuration($faker->numberBetween($min=2, $max=730))
                ->setCreatedAt($faker->dateTime(('now')))
                ->updatedTimestamps();
            
            $manager->persist($project);
        }

        $manager->flush();
    }
}
