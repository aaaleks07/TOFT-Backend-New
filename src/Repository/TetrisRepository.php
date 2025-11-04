<?php

namespace App\Repository;

use App\Entity\Tetris;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tetris>
 */
class TetrisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tetris::class);
    }

    //    /**
    //     * @return Tetris[] Returns an array of Tetris objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tetris
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Top-User nach Höchstscore (pro Visitor MAX(pkt)).
     * Rückgabe: [visitorId, maxScore]
     */
    public function findTopUsersByMaxScore(int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.fk_visitor_id', 'v')
            ->select('v.id AS visitorId, MAX(t.pkt) AS maxScore')
            ->groupBy('v.id')
            ->orderBy('maxScore', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}
