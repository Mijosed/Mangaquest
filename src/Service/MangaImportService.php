<?php

namespace App\Service;

use App\Entity\Manga;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MangaImportService
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;
    private MangaRepository $mangaRepository;

    public function __construct(
        HttpClientInterface    $client,
        EntityManagerInterface $entityManager,
        MangaRepository        $mangaRepository
    )
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->mangaRepository = $mangaRepository;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function importMangasFromApi(int $page = 1, int $limit = 10): void
    {
        $response = $this->client->request('GET', 'https://api.mangadex.org/manga', [
            'query' => [
                'limit' => $limit,
                'offset' => ($page - 1) * $limit,
            ],
        ]);

        $data = $response->toArray();

        foreach ($data['data'] as $mangaData) {
            $mangadexId = $mangaData['id'];

// Vérifier si le manga existe déjà
            $existingManga = $this->mangaRepository->findOneBy(['mangadexId' => $mangadexId]);
            if ($existingManga) {
                continue; // Sauter les mangas déjà enregistrés
            }

// Créer un nouveau Manga
            $manga = new Manga();
            $manga->setMangadexId($mangadexId);
            $manga->setTitle($mangaData['attributes']['title']['en'] ?? 'Titre inconnu');
            $manga->setDescription($mangaData['attributes']['description']['en'] ?? null);

// Ajouter d'autres champs ou relations si nécessaires

            $this->entityManager->persist($manga);
        }

        $this->entityManager->flush();
    }
}
