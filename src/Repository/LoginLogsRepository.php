<?php

namespace App\Repository;

use App\Entity\LoginLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LoginLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginLogs[]    findAll()
 * @method LoginLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginLogsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LoginLogs::class);
    }

    public function findLatestUserLoginLog($userId, $isSuccess)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :userId')
            ->andWhere('a.isSuccess = :isSuccess')
            ->setParameter(':userId', $userId)
            ->setParameters([
                ':userId' => $userId,
                ':isSuccess' => $isSuccess,
            ])
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

    }
    // /**
    //  * @return LoginLogs[] Returns an array of LoginLogs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoginLogs
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
