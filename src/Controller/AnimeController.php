<?php

namespace App\Controller;

use App\Repository\AnimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/anime')]
class AnimeController extends AbstractController
{
    #[Route('/', name: 'app_anime_index')]
    public function index(Request $request, AnimeRepository $animeRepository): Response
    {
        $searchQuery = $request->query->get('search', '');
        
        $animes = $searchQuery 
            ? $animeRepository->searchByTitle($searchQuery)
            : $animeRepository->findAll();

        return $this->render('anime/index.html.twig', [
            'animes' => $animes,
            'searchQuery' => $searchQuery,
        ]);
    }

    #[Route('/{id}', name: 'app_anime_show')]
    public function show(int $id, AnimeRepository $animeRepository): Response
    {
        $anime = $animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime not found');
        }

        return $this->render('anime/show.html.twig', [
            'anime' => $anime,
        ]);
    }
} 