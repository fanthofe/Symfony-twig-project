<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
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
        WHERE TABLE_NAME LIKE '%client%' AND (COLUMN_NAME LIKE 'id'
        OR COLUMN_NAME LIKE '%name%' OR COLUMN_NAME LIKE '%email%' 
        OR COLUMN_NAME LIKE '%enterprise%' OR COLUMN_NAME LIKE '%phone%'
        OR COLUMN_NAME LIKE '%country%' OR COLUMN_NAME LIKE '%job%'); 
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

        $tabColumn = Array("client.id", "client.first_name", "client.last_name", "client.email", 
        "client.enterprise", "client.phone", "client.country", "client.job");

        $sql = "
            SELECT client.id, client.first_name, client.last_name, client.email, 
            client.enterprise, client.phone, client.country, client.job
            FROM client
            WHERE client.status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
                AND (client.id LIKE '%".$limitSearch['value']."%' 
                OR client.first_name LIKE '%".$limitSearch['value']."%' 
                OR client.last_name LIKE '%".$limitSearch['value']."%'
                OR client.email LIKE '%".$limitSearch['value']."%'
                OR client.enterprise LIKE '%".$limitSearch['value']."%'
                OR client.phone LIKE '%".$limitSearch['value']."%'
                OR client.country LIKE '%".$limitSearch['value']."%'
                OR client.job LIKE '%".$limitSearch['value']."%')"; 

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
            FROM client
            WHERE client.status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
                AND (client.id LIKE '%".$limitSearch['value']."%' 
                OR client.first_name LIKE '%".$limitSearch['value']."%' 
                OR client.last_name LIKE '%".$limitSearch['value']."%'
                OR client.email LIKE '%".$limitSearch['value']."%'
                OR client.enterprise LIKE '%".$limitSearch['value']."%'
                OR client.phone LIKE '%".$limitSearch['value']."%'
                OR client.country LIKE '%".$limitSearch['value']."%'
                OR client.job LIKE '%".$limitSearch['value']."%')"; 
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
