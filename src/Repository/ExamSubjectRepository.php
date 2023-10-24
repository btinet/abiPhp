<?php

namespace App\Repository;

use App\Entity\ExamSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExamSubject>
 *
 * @method ExamSubject|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamSubject|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamSubject[]    findAll()
 * @method ExamSubject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamSubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamSubject::class);
    }

//    /**
//     * @return ExamSubject[] Returns an array of ExamSubject objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ExamSubject
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
