<?php

namespace App\DataFixtures;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\UserChat;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ChatFixtures extends Fixture implements OrderedFixtureInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        try {
            //code...
            for($i=1; $i<=10; $i++){
                $userChat = new UserChat();
                $chat = new Chat();

                $otherUser = $i;
                while($i == $otherUser){
                    $otherUser = random_int(1,10);
                }

                $userChat->setUserSender($this->userRepository->find(["id" => $i]))
                ->setUserReceiver($this->userRepository->find(["id" => $otherUser]))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')))
                ->updatedTimestamps(); 
                $manager->persist($userChat);

                dump($userChat->getUserReceiver(), $userChat->getUserSender());

                $chat->setUserChatId($userChat)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')))
                ->updatedTimestamps();

                $manager->persist($chat);

                for ($j = 1; $j < random_int(1,4); $j++) {
                    if($j % 2 == 0){
                        $chatMessage = new ChatMessage();
                        $chatMessage->setContent($faker->text())
                        ->setStatus('ACTIVE')
                        ->setChatId($chat)
                        ->setUserSenderId($this->userRepository->find(["id" => $i]))
                        ->setUserReceiverId($this->userRepository->find(["id" => $otherUser]))
                        ->setIsReplied($this->userRepository->find(["id" => $i]))
                        ->setHasDropdown('1')
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')))
                        ->updatedTimestamps();
    
                        $manager->persist($chatMessage);      
                        
                    } else {
                        $chatMessage = new ChatMessage();
                        $chatMessage->setContent($faker->text())
                        ->setStatus('ACTIVE')
                        ->setChatId($chat)
                        ->setUserSenderId($this->userRepository->find(["id" => $otherUser]))
                        ->setUserReceiverId($this->userRepository->find(["id" => $i]))
                        ->setIsReplied($this->userRepository->find(["id" => $otherUser]))
                        ->setHasDropdown('1')
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')))
                        ->updatedTimestamps();
    
                        $manager->persist($chatMessage); 
                    }
                }
            }
    
            $manager->flush();
        } catch (\Throwable $th) {
            dd($th);
            throw $th;
        }
    }

    public function getOrder(): int
    {
        return 4; // smaller means sooner
    }
}