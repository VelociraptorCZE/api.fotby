<?php

namespace App\Repository;

use App\Entity\LadderEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LadderEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method LadderEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method LadderEntry[]    findAll()
 * @method LadderEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LadderEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LadderEntry::class);
    }

    // /**
    //  * @return LadderEntry[] Returns an array of LadderEntry objects
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
    public function findOneBySomeField($value): ?LadderEntry
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
