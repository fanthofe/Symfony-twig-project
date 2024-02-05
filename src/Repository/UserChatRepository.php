<?php

namespace App\Repository;

use App\Entity\UserChat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserChat>
 *
 * @method UserChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserChat[]    findAll()
 * @method UserChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserChat::class);
    }

//    /**
//     * @return UserChat[] Returns an array of UserChat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserChat
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
