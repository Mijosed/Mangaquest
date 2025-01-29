<?php

namespace App\Repository;

use App\Entity\Manga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manga>
 */
class MangaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manga::class);
    }

    //    /**
    //     * @return Manga[] Returns an array of Manga objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Manga
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByPage(int $page = 1, int $limit = 24): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('m')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(string $search = null, ?string $letter = null, string $sort = 'title', string $order = 'ASC', int $page = 1, int $limit = 15): array
    {
        $offset = ($page - 1) * $limit;
        
        $qb = $this->createQueryBuilder('m')
            ->distinct();

        if ($search) {
            $qb->andWhere('m.title LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($letter) {
            $qb->andWhere('m.title LIKE :letter')
               ->setParameter('letter', $letter . '%');
        }

        $qb->orderBy('m.' . $sort, $order)
           ->setFirstResult($offset)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countBySearch(string $search = null, ?string $letter = null): int
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(DISTINCT m.id)');

        if ($search) {
            $qb->andWhere('m.title LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($letter) {
            $qb->andWhere('m.title LIKE :letter')
               ->setParameter('letter', $letter . '%');
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countByStatus(): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.status, COUNT(m.id) as count')
            ->groupBy('m.status');

        $results = $qb->getQuery()->getResult();
        
        $counts = [];
        foreach ($results as $result) {
            $counts[$result['status']] = $result['count'];
        }
        
        return $counts;
    }
}
