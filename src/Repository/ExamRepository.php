<?php

namespace App\Repository;

use App\Entity\Exam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exam>
 *
 * @method Exam|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exam|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exam[]    findAll()
 * @method Exam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exam::class);
    }

    /**
     * @return array Returns an array of Exam objects
     */
    public function sumExamPoints($pupil): array
    {
        return $this->createQueryBuilder('e')
            ->select('SUM(e.examPoints)')
            ->andWhere('e.pupil = :val')
            ->groupBy('e.pupil')
            ->setParameter('val', $pupil)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getScalarResult()
        ;
    }

//    public function findOneBySomeField($value): ?Exam
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
