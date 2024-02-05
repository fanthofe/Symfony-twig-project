<?php

namespace App\DataFixtures;

use App\Entity\Settings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SettingsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $setting = new Settings();
        $setting->setTitle('Theme Tristan')
        ->setMetaTitle('Meta Title Tristan')
        ->setMetaDescription($faker->text(20))
        ->setIsInMaintenance('1')
        ->setCreatedAt($faker->dateTime('now'))
        ->updatedTimestamps();

        $manager->persist($setting);
        $manager->flush();
    }
}
