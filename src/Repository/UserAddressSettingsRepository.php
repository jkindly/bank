<?php

namespace App\Repository;

use App\Entity\UserAddressSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserAddressSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAddressSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAddressSettings[]    findAll()
 * @method UserAddressSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAddressSettingsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserAddressSettings::class);
    }

    // /**
    //  * @return UserAddressSettings[] Returns an array of UserAddressSettings objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAddressSettings
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
