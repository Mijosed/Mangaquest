<?php

namespace App\Controller;

use App\Repository\AnimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/anime')]
class AnimeController extends AbstractController
{
    #[Route('/', name: 'app_anime_index')]
    public function index(Request $request, AnimeRepository $animeRepository): Response
    {
        $search = $request->query->get('search');
        $letter = $request->query->get('letter');
        $sort = $request->query->get('sort', 'title');
        $order = $request->query->get('order', 'ASC');
        $page = $request->query->getInt('page', 1);
        $limit = 15; // Nombre d'animes par page

        $animes = $animeRepository->findBySearch($search, $letter, $sort, $order, $page, $limit);
        $total = $animeRepository->countBySearch($search, $letter);
        
        $maxPages = ceil($total / $limit);

        // Générer l'alphabet pour le filtre
        $alphabet = range('A', 'Z');

        return $this->render('anime/index.html.twig', [
            'animes' => $animes,
            'currentPage' => $page,
            'maxPages' => $maxPages,
            'total' => $total,
            'search' => $search,
            'letter' => $letter,
            'sort' => $sort,
            'order' => $order,
            'alphabet' => $alphabet
        ]);
    }

    #[Route('/{id}', name: 'app_anime_show')]
    public function show(int $id, AnimeRepository $animeRepository): Response
    {
        $anime = $animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime non trouvé');
        }

        return $this->render('anime/show.html.twig', [
            'anime' => $anime,
        ]);
    }
}
