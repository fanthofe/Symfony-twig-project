<?php

namespace App\DataFixtures;

use App\Repository\AppRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    private $connexion;

    public function __construct(Connection $connexion)
    {
        $this->connexion = $connexion;
    }

    private function truncate()
    {
        // Unactive foreign key check to make truncate command working
        // TRUNCATE set Auto Increment and Id start at 1
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE user_internal');
        $this->connexion->executeQuery('TRUNCATE TABLE article');
        $this->connexion->executeQuery('TRUNCATE TABLE article_category');
        $this->connexion->executeQuery('TRUNCATE TABLE category');
        $this->connexion->executeQuery('TRUNCATE TABLE chat');
        $this->connexion->executeQuery('TRUNCATE TABLE chat_message');
        $this->connexion->executeQuery('TRUNCATE TABLE client');
        $this->connexion->executeQuery('TRUNCATE TABLE date_event');
        $this->connexion->executeQuery('TRUNCATE TABLE enterprise');
        $this->connexion->executeQuery('TRUNCATE TABLE project');
        $this->connexion->executeQuery('TRUNCATE TABLE settings');
        $this->connexion->executeQuery('TRUNCATE TABLE user_chat');
        $this->connexion->executeQuery('TRUNCATE TABLE user_date_event');
    }

    public function load(ObjectManager $manager): void
    {
        $this->truncate();
    }

    public function getOrder(): int
    {
        return -1; // smaller means sooner
    }
}
