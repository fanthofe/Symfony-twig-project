<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
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
        WHERE TABLE_NAME LIKE 'user_internal' AND (COLUMN_NAME LIKE 'id'
        OR COLUMN_NAME LIKE '%email%'
        OR COLUMN_NAME LIKE '%first_name%' OR COLUMN_NAME LIKE '%last_name%'); 
        ";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        $data['data'] = $dataResult;

        return $data;
    }

    /**
     * @return array
     */
    public function findDisplayUser(){
        $em = $this->getEntityManager();
        $data = [];
        $connection = $em->getConnection();

        $sql = "
        SELECT id, email, first_name, last_name 
        FROM internal.user_internal WHERE status like 'ACTIVE';        
        ";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        $data = $dataResult;

        return $data;
    }

    /**
     * @return array
     */
    public function findByProfilImage(mixed $id){
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = `
        SELECT u.profil_image 
        FROM user_internal AS u
        WHERE u.id = $id;
        `;

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        $data = $dataResult;

        return $data;
    }

    /**
     * @return array
     */
    public function findForChat(mixed $id){
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = "
        SELECT u.id, u.profil_image, u.first_name, u.last_name
        FROM user_internal AS u
        WHERE u.id = $id;
        ";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        return $dataResult;
    }

    /**
     * @return array
     */
    public function findAllChatUser(){
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = "
        SELECT u.id, u.profil_image, u.first_name, u.last_name
        FROM user_internal AS u
        ";

        $sqlPrepare = $connection->executeQuery($sql);
        $dataResult = $sqlPrepare->fetchAllAssociative();

        return $dataResult;
    }
}
