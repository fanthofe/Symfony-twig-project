<?php

namespace App\Repository;

use App\Entity\ChatImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatImage>
 *
 * @method ChatImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatImage[]    findAll()
 * @method ChatImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatImage::class);
    }

//    /**
//     * @return ChatImage[] Returns an array of ChatImage objects
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

//    public function findOneBySomeField($value): ?ChatImage
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
