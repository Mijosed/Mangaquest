<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use App\Service\MangaDexApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\FavoriteRepository;

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
    public function show(
        string $mangaDexId, 
        Request $request, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        FavoriteRepository $favoriteRepository
    ): Response
    {
        $localManga = $this->mangaRepository->findOneBy(['mangaDexId' => $mangaDexId]);
        
        if (!$localManga) {
            throw $this->createNotFoundException('Manga non trouvé');
        }

        // Check if it's a manually created manga
        $isManual = str_starts_with($mangaDexId, 'manual_');
        
        if ($isManual) {
            $mangaDetails = [
                'id' => $localManga->getMangaDexId(),
                'attributes' => [
                    'title' => ['en' => $localManga->getTitle()],
                    'description' => ['en' => $localManga->getDescription()],
                    'status' => $localManga->getStatus(),
                    'year' => $localManga->getYear(),
                    'tags' => []
                ]
            ];
            $chapters = [];
        } else {
            $mangaDetails = $this->mangaDexApi->getMangaDetails($mangaDexId);
            $chapters = $this->mangaDexApi->getMangaChapters($mangaDexId);
        }

        // Récupérer les reviews existantes
        $reviews = $entityManager->getRepository(Review::class)->findBy(
            ['manga' => $localManga],
            ['createdAt' => 'DESC']
        );

        // Créer le formulaire de review si l'utilisateur est connecté
        $review = new Review();
        $form = null;
        
        if ($this->getUser()) {
            $form = $this->createForm(ReviewType::class, $review);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $review->setUser($this->getUser());
                $review->setManga($localManga);
                
                $entityManager->persist($review);
                $entityManager->flush();

                // Envoyer l'email de notification
                $email = (new Email())
                    ->from('noreply@mangaquest.com')
                    ->to('admin@mangaquest.com')
                    ->subject('Nouvelle review manga')
                    ->html($this->renderView(
                        'emails/new_review.html.twig',
                        [
                            'review' => $review,
                            'type' => 'manga',
                            'title' => $localManga->getTitle()
                        ]
                    ));

                $mailer->send($email);

                $this->addFlash('success', 'Votre avis a été publié avec succès !');
                return $this->redirectToRoute('app_manga_show', ['mangaDexId' => $mangaDexId]);
            }
        }

        // Vérifier si le manga est en favori pour l'utilisateur connecté
        $favorite = null;
        if ($this->getUser()) {
            $favorite = $favoriteRepository->findOneByUserAndManga($this->getUser(), $localManga);
        }

        return $this->render('manga/show.html.twig', [
            'manga' => $mangaDetails,
            'localManga' => $localManga,
            'chapters' => $chapters,
            'reviews' => $reviews,
            'form' => $form?->createView(),
            'favorite' => $favorite,
        ]);
    }

    #[Route('/manga/{mangaDexId}/chapter/{chapterId}', name: 'app_manga_chapter')]
    public function chapter(string $mangaDexId, string $chapterId): Response
    {
        $localManga = $this->mangaRepository->findOneBy(['mangaDexId' => $mangaDexId]);
        
        if (!$localManga) {
            throw $this->createNotFoundException('Manga non trouvé');
        }

        $chapterPages = $this->mangaDexApi->getChapterPages($chapterId);

        return $this->render('manga/chapter.html.twig', [
            'manga' => $localManga,
            'pages' => $chapterPages
        ]);
    }
}
