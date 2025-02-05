<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\AnimeRepository;
use App\Repository\FavoriteRepository;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('ROLE_USER')]
class FavoriteController extends AbstractController
{
    #[Route('/favorites', name: 'app_favorites')]
    public function index(FavoriteRepository $favoriteRepository): Response
    {
        $favorites = $favoriteRepository->findUserFavorites($this->getUser());

        return $this->render('favorite/index.html.twig', [
            'favorites' => $favorites
        ]);
    }

    #[Route('/favorite/manga/{id}', name: 'app_favorite_manga')]
    public function toggleMangaFavorite(
        string $id,
        MangaRepository $mangaRepository,
        FavoriteRepository $favoriteRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $manga = $mangaRepository->findOneBy(['mangaDexId' => $id]);
        
        if (!$manga) {
            return new JsonResponse(['error' => 'Manga non trouvé'], 404);
        }

        $favorite = $favoriteRepository->findOneByUserAndManga($this->getUser(), $manga);

        if ($favorite) {
            $entityManager->remove($favorite);
            $status = 'removed';
        } else {
            $favorite = new Favorite();
            $favorite->setUser($this->getUser());
            $favorite->setManga($manga);
            $entityManager->persist($favorite);
            $status = 'added';
        }

        $entityManager->flush();

        return new JsonResponse(['status' => $status]);
    }

    #[Route('/favorite/anime/{id}', name: 'app_favorite_anime')]
    public function toggleAnimeFavorite(
        int $id,
        AnimeRepository $animeRepository,
        FavoriteRepository $favoriteRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $anime = $animeRepository->find($id);
        
        if (!$anime) {
            return new JsonResponse(['error' => 'Anime non trouvé'], 404);
        }

        $favorite = $favoriteRepository->findOneByUserAndAnime($this->getUser(), $anime);

        if ($favorite) {
            $entityManager->remove($favorite);
            $status = 'removed';
        } else {
            $favorite = new Favorite();
            $favorite->setUser($this->getUser());
            $favorite->setAnime($anime);
            $entityManager->persist($favorite);
            $status = 'added';
        }

        $entityManager->flush();

        return new JsonResponse(['status' => $status]);
    }
} 