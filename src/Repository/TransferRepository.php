<?php

namespace App\Repository;

use App\Entity\Transfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transfer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transfer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transfer[]    findAll()
 * @method Transfer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransferRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transfer::class);
    }

    public function findAllTransfersLimit5(string $accountNumber)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.senderAccountNumber = :accNumber')
            ->orWhere('t.receiverAccountNumber = :accNumber')
            ->setParameter(':accNumber', $accountNumber)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNext5Transfers(int $transferId, string $accountNumber)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.senderAccountNumber = :accNumber')
            ->orWhere('t.receiverAccountNumber = :accNumber')
            ->andWhere('t.id < :transferId')
            ->setParameters([
                'accNumber' => $accountNumber,
                'transferId' => $transferId
            ])
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findTransferDetails(int $transferId, int $userId)
//    {
//        return $this->createQueryBuilder('t')
//            // p.category refers to the "category" property on product
//            ->innerJoin('t.user', 'u')
//            // selects all the category data to avoid the query
//            ->addSelect('u')
//            ->andWhere('t.user = :userId')
//            ->andWhere('t.id = :transferId')
//            ->setParameters([
//                'userId' => $userId,
//                'transferId' => $transferId
//            ])
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    // /**
    //  * @return Transfer[] Returns an array of Transfer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transfer
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
