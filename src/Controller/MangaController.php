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
        $search = $request->query->get('search');
        $letter = $request->query->get('letter');
        $sort = $request->query->get('sort', 'title');
        $order = $request->query->get('order', 'ASC');
        $page = $request->query->getInt('page', 1);
        $limit = 15;  // 15 mangas par page (5 x 3)

        $mangas = $this->mangaRepository->findBySearch($search, $letter, $sort, $order, $page, $limit);
        $total = $this->mangaRepository->countBySearch($search, $letter);
        
        $maxPages = ceil($total / $limit);

        // Générer l'alphabet pour le filtre
        $alphabet = range('A', 'Z');

        return $this->render('manga/index.html.twig', [
            'mangas' => $mangas,
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
