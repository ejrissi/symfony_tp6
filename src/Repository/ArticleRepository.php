<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

     /**
     * Finds articles within a specific price range.
     *
     * @param float|null $minValue The minimum price
     * @param float|null $maxValue The maximum price
     *
     * @return Article[] Returns an array of Article objects
     */
    public function findByPriceRange($minValue, $maxValue)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.Prix >= :minVal')
            ->setParameter('minVal', $minValue)
            ->andWhere('a.Prix <= :maxVal')
            ->setParameter('maxVal', $maxValue)
            ->orderBy('a.id', 'ASC') // Optionally change this to any other ordering criterion
            ->setMaxResults(10) // Optionally set the limit for results
            ->getQuery()
            ->getResult();
    }


    

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
