<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use App\Service\MangaDexApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaController extends AbstractController
{
    public function __construct(
        private readonly MangaDexApiService $mangaDexApi,
        private readonly MangaRepository $mangaRepository
    ) {}

    #[Route('/manga', name: 'app_manga_list')]
    public function index(): Response
    {
        $mangas = $this->mangaRepository->findAll();
        
        // Debug temporaire
        dump($mangas);
        
        return $this->render('manga/index.html.twig', [
            'mangas' => $mangas
        ]);
    }

    #[Route('/manga/{mangaDexId}', name: 'app_manga_show')]
    public function show(string $mangaDexId): Response
    {
        $localManga = $this->mangaRepository->findOneBy(['mangaDexId' => $mangaDexId]);
        $mangaDetails = $this->mangaDexApi->getMangaDetails($mangaDexId);

        return $this->render('manga/show.html.twig', [
            'manga' => $mangaDetails,
            'localManga' => $localManga
        ]);
    }
}
