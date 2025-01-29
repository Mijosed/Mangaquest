<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MangaDexApiService
{
    private const API_BASE_URL = 'https://api.mangadex.org';

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}

    public function getMangaList(int $limit = 50): array
    {
        $response = $this->httpClient->request('GET', self::API_BASE_URL . '/manga', [
            'query' => [
                'limit' => $limit,
                'includes[]' => 'cover_art',
                'contentRating[]' => 'safe',
                'availableTranslatedLanguage[]' => 'en',
                'hasAvailableChapters' => true,
                'order[followedCount]' => 'desc',
                'offset' => 0
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Referer' => 'https://mangadex.org'
            ]
        ]);

        return $response->toArray()['data'];
    }

    public function getMangaDetails(string $mangaId): array
    {
        $response = $this->httpClient->request('GET', self::API_BASE_URL . "/manga/{$mangaId}", [
            'query' => [
                'includes[]' => 'cover_art',
                'contentRating[]' => 'safe'
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Referer' => 'https://mangadex.org'
            ]
        ]);

        return $response->toArray()['data'];
    }
} 