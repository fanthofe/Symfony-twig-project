<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixtures extends Fixture implements OrderedFixtureInterface
{
    private $_slug;

    public function __construct(SluggerInterface $slug){
        $this->_slug = $slug;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i=1; $i<=30; $i++){
            $article = new Article();
            $choice = $faker->numberBetween(1,2);
            
            $article->setTitle($faker->sentence(5, true))
                ->setSlug($this->_slug->slug(strtolower($article->getTitle())))
                ->setImage($faker->imageUrl(200, 200, 'cats', true))
                ->setContent($faker->text(5000))
                ->setShortDescription($faker->sentence(rand(5,9)))
                ->setDate($faker->dateTimeBetween('next Monday', 'next Monday +20 days'))
                ->setStatus($choice == 1 ? 'DRAFT' : 'PUBLISHED')
                ->setCreatedAt($faker->dateTime('now'))
                ->updatedTimestamps();
            $manager->persist($article);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 0; // smaller means sooner
    }
}