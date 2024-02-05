<?php

namespace App\DataFixtures;

use App\Entity\ArticleCategory;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleCategoryFixtures extends Fixture implements OrderedFixtureInterface
{
    private $articleRepo;
    private $categoryRepo;

    public function __construct(ArticleRepository $articleRepo, CategoryRepository $categoryRepo)
    {
        $this->articleRepo = $articleRepo;
        $this->categoryRepo = $categoryRepo;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $articles = $this->articleRepo->findAll();
        $categories = $this->categoryRepo->findAll();

        foreach($articles as $article){
            for($i = 0; $i <= rand(1, 3); $i++){
                $articleCategory = new ArticleCategory;
                $articleCategory->setArticle($article);
                $articleCategory->setCategory($categories[rand(0, (count($categories)-1))])  
                    ->setCreatedAt($faker->dateTime('now'))
                    ->updatedTimestamps();
                $manager->persist($articleCategory);
            }
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2; // smaller means sooner
    }
}