<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    // /**
    //  * @return Payment[] Returns an array of Payment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getPromedyByMonth($month, $year)
    {
        $config = new \Doctrine\ORM\Configuration();
        $config->addCustomNumericFunction('month', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
        $config->addCustomNumericFunction('year', 'Oro\ORM\Query\AST\Functions\SimpleFunction');

        if ($month > 12 || $month < 1) {
            return "WRONG MONTH DATA";
        }

        $sum = $this->createQueryBuilder('p')
            ->where('MONTH(p.date) =  :month')
            ->andWhere('YEAR(p.date) =  :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->select('SUM(p.percent) as percent')
            ->getQuery()
            ->getOneOrNullResult();
        $days = $this->createQueryBuilder('p')
            ->where('MONTH(p.date) =  :month')
            ->andWhere('YEAR(p.date) =  :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->select('COUNT(p.id) as id')
            ->getQuery()
            ->getOneOrNullResult();

        $sum = $sum['percent'];
        $days = $days['id'];

        if ($sum == 0 || $days == 0) {
            return "NOT EXISTING DATA";
        }

        return $sum / $days;
    }

    public function getPaymentByDay($day, $month, $year)
    {

        $payment = $this->createQueryBuilder('p')
            ->where('DAY(p.date) = :day')
            ->andWhere('MONTH(p.date) = :month')
            ->andWhere('YEAR(p.date) = :year')
            ->setParameter('day', $day)
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->select('SUM(p.percent) as percent')
            ->getQuery()
            ->getOneOrNullResult();
        return $payment['percent'];
    }
}
