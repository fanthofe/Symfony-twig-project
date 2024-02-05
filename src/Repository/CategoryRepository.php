<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return array
     */
    public function findAllAjax($start, $length, $order, $limitSearch)
    {

        $em = $this->getEntityManager();
        $data = [];
        $where= " ";
        $connection = $em->getConnection();

        $tabColumn = Array("category.id", "category.name");

        $sql = "
            SELECT category.id, category.name
            FROM category
            WHERE category.status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
                AND (category.id LIKE '%".$limitSearch['value']."%' 
                OR category.name LIKE '%".$limitSearch['value']."%')"; 
        }

        if($order){
            $where .= " 
                ORDER BY ".$tabColumn[$order[0]['column']]." ".$order[0]['dir'];
        }


        $where .= " LIMIT ".$length." OFFSET ".$start;

        $sqlPrepare = $connection->executeQuery($sql.$where);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        /* FOR COUNT TOTAL */
        $where = " ";
        $sqlCount = "
            SELECT count(*) as numbertotal
            FROM category
            WHERE category.status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
            AND (category.id LIKE '%".$limitSearch['value']."%' 
            OR category.name LIKE '%".$limitSearch['value']."%')"; 
        }

        $sqlPrepare = $connection->executeQuery($sqlCount.$where);
        $dataResultCount = $sqlPrepare->fetchAllAssociative();

        if(isset($dataResultCount[0]['numbertotal'])){
            $data['length'] = $dataResultCount[0]['numbertotal'];
        }
        else{
            $data['length'] = 0;
        }
        
        $data['data'] = $dataResult;

        return $data;
    }
}
