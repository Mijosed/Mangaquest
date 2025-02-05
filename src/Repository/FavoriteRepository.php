<?php

namespace App\Repository;

use App\Entity\Favorite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function findUserFavorites(User $user)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByUserAndManga(User $user, $manga): ?Favorite
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->andWhere('f.manga = :manga')
            ->setParameter('user', $user)
            ->setParameter('manga', $manga)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByUserAndAnime(User $user, $anime): ?Favorite
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->andWhere('f.anime = :anime')
            ->setParameter('user', $user)
            ->setParameter('anime', $anime)
            ->getQuery()
            ->getOneOrNullResult();
    }
} 