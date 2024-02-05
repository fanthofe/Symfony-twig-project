<?php

namespace App\DataFixtures;

use App\Entity\DateEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DateEventFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i=1; $i<=15; $i++){
            $dateEvent = new DateEvent();
            $start = $faker->dateTimeBetween('next Monday', 'next Monday +7 days');
            $end = $faker->dateTimeBetween($start, $start->format('Y-m-d H:i:s').' +2 days');
            
            $dateEvent->setStartDate($start)
                ->setEndDate($end)
                ->setName("Event{$i}")
                ->setLocalisation($faker->city())
                ->setDescription($faker->sentence())
                ->setCreatedAt($faker->dateTime('now'))
                ->updatedTimestamps();
            $manager->persist($dateEvent);
        }

        $manager->flush();
    }
}