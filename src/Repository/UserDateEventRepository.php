<?php

namespace App\Repository;

use App\Entity\UserDateEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDateEvent>
 *
 * @method UserDateEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDateEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDateEvent[]    findAll()
 * @method UserDateEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDateEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDateEvent::class);
    }

//    /**
//     * @return UserDateEvent[] Returns an array of UserDateEvent objects
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

//    public function findOneBySomeField($value): ?UserDateEvent
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
