<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
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
        WHERE TABLE_NAME LIKE 'article' AND (COLUMN_NAME LIKE 'id'
        OR COLUMN_NAME LIKE '%title%'
        OR COLUMN_NAME LIKE 'date' OR COLUMN_NAME LIKE 'status'); 
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

        $tabColumn = Array("article.id", "article.title", "article.date", "article.status");

        $sql = "
            SELECT article.id, article.title, article.date, article.status
            FROM article
            WHERE article.data_status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
                AND (article.id LIKE '%".$limitSearch['value']."%' 
                OR article.title LIKE '%".$limitSearch['value']."%' 
                OR article.date LIKE '%".$limitSearch['value']."%'
                OR article.status LIKE '%".$limitSearch['value']."%')"; 
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
            FROM article
            WHERE article.data_status = 'ACTIVE'
        ";

        if($limitSearch["value"] != ""){
            $where .= "
            AND (article.id LIKE '%".$limitSearch['value']."%' 
            OR article.title LIKE '%".$limitSearch['value']."%' 
            OR article.date LIKE '%".$limitSearch['value']."%'
            OR article.status LIKE '%".$limitSearch['value']."%')"; 
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

    public function findArticlesPagination(int $page, int $limit = 4): array
    {
        $limit = abs($limit);
        
        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('a')
                ->from('App\Entity\Article', 'a')
                ->where("a.dataStatus = 'ACTIVE'")
                ->orderBy("a.date","DESC")
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

    public function lengthAllArticles()
    {
        $query = $this->getEntityManager()->createQueryBuilder()
        ->select('COUNT(1)')
        ->from('App\Entity\Article', 'a')
        ->where("a.dataStatus = 'ACTIVE'");

        $data = $query->getQuery()->getSingleScalarResult();

        return $data;
    }

    public function checkExistingSlug(string $slug)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
        ->select('a.slug')
        ->from('App\Entity\Article', 'a')
        ->where("SUBSTRING(a.slug,1 , LENGTH('". $slug ."')) = '" . $slug . "' AND a.dataStatus = 'ACTIVE'");

        $data = $query->getQuery()->getResult();

        if(count($data) == 1){
            $lastCharacterSlug = substr($data[0]['slug'], strlen($slug) + 1, strlen($data[0]['slug']));

            if(substr($data[0]['slug'], 0, strlen($slug)) == $slug){
                $slugCollection[] = $lastCharacterSlug == "" ? 1 : intval($lastCharacterSlug, 10);
            }

        } else if (count($data) > 1){

            foreach($data as $article){

                $lastCharacterSlug = substr($article['slug'], strlen($slug) + 1, strlen($article['slug']));

                if(substr($article['slug'], 0, strlen($slug)) == $slug){
                    $slugCollection[] = $lastCharacterSlug == "" ? 1 : intval($lastCharacterSlug, 10);
                }
            }
        } else {
            return $data;
        }

        return $slugCollection;
    }
}
