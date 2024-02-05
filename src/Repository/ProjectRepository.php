<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
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
        WHERE TABLE_NAME LIKE '%project%' AND (COLUMN_NAME LIKE 'id'
        OR COLUMN_NAME LIKE '%name%' OR COLUMN_NAME LIKE '%ressource%' 
        OR COLUMN_NAME LIKE '%estimation_duration%'); 
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

        $tabColumn = Array("project.id", "project.name", "project.ressource","project.estimation_duration");

        $sql = "
            SELECT project.id, project.name, project.ressource, project.estimation_duration
            FROM project
            WHERE project.status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
                AND (project.id LIKE '%".$limitSearch['value']."%' 
                OR project.name LIKE '%".$limitSearch['value']."%' 
                OR project.ressource LIKE '%".$limitSearch['value']."%'
                OR project.estimation_duration LIKE '%".$limitSearch['value']."%')";
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
            FROM project
            WHERE project.status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
                AND (project.id LIKE '%".$limitSearch['value']."%' 
                OR project.name LIKE '%".$limitSearch['value']."%' 
                OR project.ressource LIKE '%".$limitSearch['value']."%'
                OR project.estimation_duration LIKE '%".$limitSearch['value']."%')";
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
