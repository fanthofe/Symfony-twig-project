<?php

namespace App\Repository;

use App\Entity\DateEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DateEvent>
 *
 * @method DateEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateEvent[]    findAll()
 * @method DateEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DateEvent::class);
    }

    public function findEventsPagination(int $page, int $limit = 4): array
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('e')
                ->from('App\Entity\DateEvent', 'e')
                ->where("e.status = 'ACTIVE'")
                ->setMaxResults($limit)
                ->setFirstResult(($page * $limit) - $limit);

        $paginator = new Paginator($query);

        $data = $paginator->getQuery()->getResult();

        if(empty($data)){
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;
        
        return $result;
    }

    public function lengthAllEvents()
    {
        $query = $this->getEntityManager()->createQueryBuilder()
        ->select('COUNT(1)')
        ->from('App\Entity\DateEvent', 'a')
        ->where("a.status = 'ACTIVE'");

        $data = $query->getQuery()->getSingleScalarResult();

        return $data;
    }

//    /**
//     * @return DateEvent[] Returns an array of DateEvent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DateEvent
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
