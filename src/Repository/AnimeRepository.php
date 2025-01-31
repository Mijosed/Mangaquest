<?php

namespace App\Repository;

use App\Entity\Anime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Anime>
 */
class AnimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anime::class);
    }

    public function findBySearch(string $search = null, ?string $letter = null, string $sort = 'title', string $order = 'ASC', int $page = 1, int $limit = 15): array
    {
        $offset = ($page - 1) * $limit;
        
        $qb = $this->createQueryBuilder('a')
            ->distinct();

        if ($search) {
            $qb->andWhere('LOWER(a.title) LIKE LOWER(:search)')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($letter) {
            $qb->andWhere('a.title LIKE :letter')
               ->setParameter('letter', $letter . '%');
        }

        if ($sort === 'year') {
            $qb->orderBy('a.releaseDate', $order);
        } else {
            $qb->orderBy('a.' . $sort, $order);
        }

        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countBySearch(string $search = null, ?string $letter = null): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(DISTINCT a.id)');

        if ($search) {
            $qb->andWhere('LOWER(a.title) LIKE LOWER(:search)')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($letter) {
            $qb->andWhere('a.title LIKE :letter')
               ->setParameter('letter', $letter . '%');
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByTitles(array $titles): array
{
    return $this->createQueryBuilder('a')
        ->where('a.title IN (:titles)') 
        ->setParameter('titles', $titles)
        ->getQuery()
        ->getResult();
}
}
