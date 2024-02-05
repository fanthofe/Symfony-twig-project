<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private $userPasswordHasherInterface;

    public function __construct (UserPasswordHasherInterface $userPasswordHasherInterface) 
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i=1; $i <= 10; $i++){
            $user = new User();
            $user->setEmail($faker->email)
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setJob($faker->jobTitle())
                ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
                ->setPhone($faker->numerify('07##########'))
                ->setCountry($faker->country())
                ->setProfilImage($faker->imageUrl(200, 200, 'people', true))
                ->setDateEntry($faker->dateTime('now'))
                ->setPassword($this->userPasswordHasherInterface->hashPassword(
                    $user, "password"
                ))
                ->setCreatedAt($faker->dateTime('now'))
                ->updatedTimestamps();

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 3; // smaller means sooner
    }
}
