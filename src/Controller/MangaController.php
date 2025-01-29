<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use App\Service\MangaDexApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaController extends AbstractController
{
    public function __construct(
        private readonly MangaDexApiService $mangaDexApi,
        private readonly MangaRepository $mangaRepository
    ) {}

    #[Route('/manga', name: 'app_manga_list')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 24; // nombre de mangas par page

        $mangas = $this->mangaRepository->findByPage($page, $limit);
        $total = $this->mangaRepository->count([]);
        
        $maxPages = ceil($total / $limit);

        return $this->render('manga/index.html.twig', [
            'mangas' => $mangas,
            'currentPage' => $page,
            'maxPages' => $maxPages,
            'total' => $total
        ]);
    }

    #[Route('/manga/{mangaDexId}', name: 'app_manga_show')]
    public function show(string $mangaDexId): Response
    {
        $localManga = $this->mangaRepository->findOneBy(['mangaDexId' => $mangaDexId]);
        
        if (!$localManga) {
            throw $this->createNotFoundException('Manga non trouvé');
        }

        $mangaDetails = $this->mangaDexApi->getMangaDetails($mangaDexId);

        return $this->render('manga/show.html.twig', [
            'manga' => $mangaDetails,
            'localManga' => $localManga
        ]);
    }
}
