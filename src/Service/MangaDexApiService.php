<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MangaDexApiService
{
    private const API_BASE_URL = 'https://api.mangadex.org';

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}

    public function getMangaList(int $page = 1, int $limit = 50): array
    {
        $offset = ($page - 1) * $limit;
        
        $response = $this->httpClient->request('GET', self::API_BASE_URL . '/manga', [
            'query' => [
                'limit' => $limit,
                'offset' => $offset,
                'includes[]' => 'cover_art',
                'contentRating[]' => 'safe',
                'availableTranslatedLanguage[]' => 'en',
                'hasAvailableChapters' => true,
                'order[followedCount]' => 'desc'
            ],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $data = $response->toArray();
        
        // Vérification de la structure des données
        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new \RuntimeException('Format de données invalide reçu de l\'API: ' . json_encode($data));
        }

        return [
            'data' => $data['data'],
            'total' => $data['total'] ?? 0,
            'limit' => $data['limit'] ?? $limit,
            'offset' => $data['offset'] ?? $offset
        ];
    }

    public function getMangaDetails(string $mangaId): array
    {
        $response = $this->httpClient->request('GET', self::API_BASE_URL . "/manga/{$mangaId}", [
            'query' => [
                'includes[]' => 'cover_art',
                'contentRating[]' => 'safe'
            ],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        return $response->toArray()['data'];
    }
} 