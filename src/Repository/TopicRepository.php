<?php

namespace App\Repository;

use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function findFilteredTopics(array $filters = [], int $page = 1, int $limit = 10): array
    {
        // Création de la requête de base
        $queryBuilder = $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'a')
            ->addSelect('a')
            ->where('t.isApproved = :isApproved')
            ->setParameter('isApproved', true);

        // Filtre par type
        if (!empty($filters['type'])) {
            switch ($filters['type']) {
                case 'MangaTopic':
                    $queryBuilder->andWhere('t INSTANCE OF App\\Entity\\MangaTopic');
                    break;
                case 'AnimeTopic':
                    $queryBuilder->andWhere('t INSTANCE OF App\\Entity\\AnimeTopic');
                    break;
                case 'Topic':
                    $queryBuilder->andWhere('t NOT INSTANCE OF App\\Entity\\MangaTopic')
                              ->andWhere('t NOT INSTANCE OF App\\Entity\\AnimeTopic');
                    break;
            }
        }

        // Filtre par date
        if (!empty($filters['date'])) {
            switch ($filters['date']) {
                case 'today':
                    $queryBuilder->andWhere('t.createdAt >= :today')
                              ->setParameter('today', new \DateTime('today'));
                    break;
                case 'week':
                    $queryBuilder->andWhere('t.createdAt >= :week')
                              ->setParameter('week', new \DateTime('-1 week'));
                    break;
                case 'month':
                    $queryBuilder->andWhere('t.createdAt >= :month')
                              ->setParameter('month', new \DateTime('-1 month'));
                    break;
            }
        }

        // Filtre NSFW
        if (isset($filters['nsfw'])) {
            $queryBuilder->andWhere('t.isNsfw = :nsfw')
                      ->setParameter('nsfw', $filters['nsfw']);
        }

        // Compter le total avant d'appliquer la pagination
        $countQuery = clone $queryBuilder;
        $total = count($countQuery->getQuery()->getResult());

        // Ajouter le tri et la pagination
        $queryBuilder->orderBy('t.' . ($filters['sort'] ?? 'createdAt'), $filters['order'] ?? 'DESC')
                     ->setFirstResult(($page - 1) * $limit)
                     ->setMaxResults($limit);

        return [
            'topics' => $queryBuilder->getQuery()->getResult(),
            'totalItems' => $total,
            'currentPage' => $page,
            'lastPage' => ceil($total / $limit)
        ];
    }
}
