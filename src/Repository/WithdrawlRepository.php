<?php

namespace App\Repository;

use App\Entity\Withdrawl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Withdrawl|null find($id, $lockMode = null, $lockVersion = null)
 * @method Withdrawl|null findOneBy(array $criteria, array $orderBy = null)
 * @method Withdrawl[]    findAll()
 * @method Withdrawl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WithdrawlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Withdrawl::class);
    }

    // /**
    //  * @return Withdrawl[] Returns an array of Withdrawl objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Withdrawl
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
