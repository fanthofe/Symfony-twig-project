<?php

namespace App\Repository;

use App\Entity\Enterprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enterprise>
 *
 * @method Enterprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enterprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enterprise[]    findAll()
 * @method Enterprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnterpriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enterprise::class);
    }

    /**
     * @return array
     */
    public function findColumnNameDatabase(){
        $em = $this->getEntityManager();
        $data = [];
        $connection = $em->getConnection();

        $sql = "
        SELECT COLUMN_NAME, TABLE_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME LIKE '%enterprise%' AND (COLUMN_NAME LIKE 'id'
        OR COLUMN_NAME LIKE '%name%' OR COLUMN_NAME LIKE '%expertise_field%' 
        OR COLUMN_NAME LIKE '%number_director%' OR COLUMN_NAME LIKE '%address%'
        OR COLUMN_NAME LIKE '%city%' OR COLUMN_NAME LIKE '%country%' OR COLUMN_NAME LIKE '%phone_number%'
        OR COLUMN_NAME LIKE '%siret%' OR COLUMN_NAME LIKE '%creation_date%'); 
        ";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        $data['data'] = $dataResult;

        return $data;
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

        $tabColumn = Array("enterprise.id", "enterprise.name", "enterprise.expertise_field", "enterprise.address", 
        "enterprise.city", "enterprise.country", "enterprise.phone_number", "enterprise.siret", "enterprise.creation_date","enterprise.number_director");

        $sql = "
            SELECT enterprise.id, enterprise.name, enterprise.expertise_field, enterprise.address, 
            enterprise.city, enterprise.country, enterprise.phone_number, enterprise.siret, enterprise.creation_date, enterprise.number_director
            FROM enterprise
            WHERE enterprise.status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
                AND (enterprise.id LIKE '%".$limitSearch['value']."%' 
                OR enterprise.name LIKE '%".$limitSearch['value']."%' 
                OR enterprise.expertise_field LIKE '%".$limitSearch['value']."%'
                OR enterprise.address LIKE '%".$limitSearch['value']."%'
                OR enterprise.city LIKE '%".$limitSearch['value']."%'
                OR enterprise.country LIKE '%".$limitSearch['value']."%'
                OR enterprise.phone_number LIKE '%".$limitSearch['value']."%'
                OR enterprise.siret LIKE '%".$limitSearch['value']."%'
                OR enterprise.creation_date LIKE '%".$limitSearch['value']."%'
                OR enterprise.number_director LIKE '%".$limitSearch['value']."%')"; 
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
            FROM enterprise
            WHERE enterprise.status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
            AND (enterprise.id LIKE '%".$limitSearch['value']."%' 
            OR enterprise.name LIKE '%".$limitSearch['value']."%' 
            OR enterprise.expertise_field LIKE '%".$limitSearch['value']."%'
            OR enterprise.address LIKE '%".$limitSearch['value']."%'
            OR enterprise.city LIKE '%".$limitSearch['value']."%'
            OR enterprise.country LIKE '%".$limitSearch['value']."%'
            OR enterprise.phone_number LIKE '%".$limitSearch['value']."%'
            OR enterprise.siret LIKE '%".$limitSearch['value']."%'
            OR enterprise.creation_date LIKE '%".$limitSearch['value']."%'
            OR enterprise.number_director LIKE '%".$limitSearch['value']."%')"; 
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
