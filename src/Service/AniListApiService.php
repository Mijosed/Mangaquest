<?php

namespace App\Service;

use App\Entity\Anime;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use DateTimeImmutable;

class AniListApiService
{
    private const API_URL = 'https://graphql.anilist.co';

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}

    public function fetchPopularAnime(int $page = 1, int $perPage = 50): array
    {
        $query = <<<'GRAPHQL'
        query ($page: Int, $perPage: Int) {
            Page(page: $page, perPage: $perPage) {
                media(type: ANIME, sort: POPULARITY_DESC) {
                    id
                    title {
                        romaji
                    }
                    description
                    episodes
                    genres
                    studios {
                        nodes {
                            name
                        }
                    }
                    startDate {
                        year
                        month
                        day
                    }
                    coverImage {
                        large
                    }
                }
            }
        }
        GRAPHQL;

        $response = $this->httpClient->request('POST', self::API_URL, [
            'json' => [
                'query' => $query,
                'variables' => [
                    'page' => $page,
                    'perPage' => $perPage
                ]
            ]
        ]);

        return $response->toArray()['data']['Page']['media'];
    }

    public function mapToAnimeEntity(array $apiData): Anime
    {
        $anime = new Anime();
        $anime->setTitle($apiData['title']['romaji']);
        $anime->setDescription($apiData['description'] ?? 'No description available');
        $anime->setEpisodes($apiData['episodes'] ?? 0);
        $anime->setStudio($apiData['studios']['nodes'][0]['name'] ?? 'Unknown Studio');
        $anime->setGenre(implode(', ', $apiData['genres'] ?? []));
        
        if (isset($apiData['startDate']['year'])) {
            $date = new \DateTime();
            $date->setDate(
                $apiData['startDate']['year'],
                $apiData['startDate']['month'] ?? 1,
                $apiData['startDate']['day'] ?? 1
            );
            $anime->setReleaseDate($date);
        }
        
        $anime->setPosterImage($apiData['coverImage']['large'] ?? '');

        return $anime;
    }
} 