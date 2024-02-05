<?php

namespace App\DataFixtures;

use App\Entity\UserDateEvent;
use App\Repository\DateEventRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserDateEventFixtures extends Fixture
{
    private $em;
    private $userRepository;

    public function __construct(DateEventRepository $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $listEvent = $this->em->findAll();
        $users = $this->userRepository->findAll();

        foreach($listEvent as $event){
            for($i = 0; $i <= rand(5, 8); $i++){
                $userDateEvent = new UserDateEvent;
                $userDateEvent->setDateEvent($event);
                $userDateEvent->setUser($users[rand(0, (count($users)-1))]);  
                $manager->persist($userDateEvent);
            }
        }

        $manager->flush();
    }
}