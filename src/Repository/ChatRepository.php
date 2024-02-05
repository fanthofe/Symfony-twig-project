<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 *
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    /**
    * @return User[] Returns all arrays of Users which speak to current User
    */
    public function filterChatByCurrentUser(int $userId)
    {
        $em = $this->getEntityManager();
        $data = [];
        $connection = $em->getConnection();

        $sql = "
        SELECT chat.user_chat_id_id, user_chat.user_sender_id, user_chat.user_receiver_id
        FROM chat
        LEFT JOIN user_chat ON chat.user_chat_id_id = user_chat.id
        WHERE user_chat.user_sender_id = '". $userId ."' OR user_chat.user_receiver_id = '". $userId ."';";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        if(count($dataResult) == 1){
            $data[0] = $this->findSpeakerFromCurrentUser($userId, $dataResult[0]['user_receiver_id'], $dataResult[0]['user_sender_id']);
        } else {
            for ($i = 0; $i < count($dataResult); $i++) {
                $data[$i] = $this->findSpeakerFromCurrentUser($userId, $dataResult[$i]['user_receiver_id'], $dataResult[$i]['user_sender_id']);
            }
        }

        return $data;
    }

    /**
    * @return User[] Returns an array of Users which speak to current User
    */
    public function findSpeakerFromCurrentUser(int $currentUserId, int $receiverId, int $senderId)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $id = $currentUserId == $receiverId ? $senderId : $receiverId;

        $sql = "
        SELECT user_internal.id, user_internal.first_name, user_internal.last_name, user_internal.profil_image
        FROM user_internal
        WHERE user_internal.id = '". $id ."';";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        return $dataResult;
    }

    public function loadChatMessageFromSpeakerAjax(int $speakerId, int $currentUserId)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = "
        SELECT c.id, c.chat_id_id, c.content, c.created_at, c.has_dropdown, c.is_replied_id, c.user_receiver_id_id, c.user_sender_id_id, 
        u.profil_image AS sender_image, u.first_name AS sender_first_name,
        u.last_name AS sender_last_name, u1.first_name AS receiver_first_name,
        u1.last_name AS receiver_last_name, i.image AS chat_image
        FROM chat_message AS c
        LEFT JOIN user_internal u ON c.user_sender_id_id = u.id
        LEFT JOIN user_internal u1 ON c.user_receiver_id_id = u1.id
        LEFT JOIN chat_image i ON c.image_id = i.id
        WHERE (c.user_sender_id_id = '". $speakerId ."' AND c.user_receiver_id_id = '". $currentUserId ."') OR (c.user_receiver_id_id = '". $speakerId ."' AND c.user_sender_id_id = '". $currentUserId ."')
        AND c.status = 'ACTIVE'
        ORDER BY c.id ASC;
        ";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        return $dataResult;
    }

    public function findChatId(int $speakerId, int $currentUserId)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = "
        SELECT c.id
        FROM user_chat AS c
        WHERE (c.user_sender_id = '". $speakerId ."' AND c.user_receiver_id = '". $currentUserId ."') OR (c.user_receiver_id = '". $speakerId ."' AND c.user_sender_id = '". $currentUserId ."');
        ";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchOne();

        return $dataResult;
    }

    public function isChatUserExist(int $chatUserId, int $currentUserId)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = "
        SELECT *
        FROM user_chat AS c
        WHERE (c.user_sender_id = '". $chatUserId ."' AND c.user_receiver_id = '". $currentUserId ."') OR (c.user_receiver_id = '". $chatUserId ."' AND c.user_sender_id = '". $currentUserId ."');
        ";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchOne();

        if(!$dataResult){
            return false;
        }

        return $chatUserId;
    }

    

//    /**
//     * @return Chat[] Returns an array of Chat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Chat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
