<?php

namespace App\Command;

use App\Entity\Manga;
use App\Service\MangaDexApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-manga',
    description: 'Importe les mangas depuis MangaDex API'
)]
class ImportMangaCommand extends Command
{
    public function __construct(
        private readonly MangaDexApiService $mangaDexApi,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->info('Début de l\'importation des mangas...');
        
        try {
            $mangas = $this->mangaDexApi->getMangaList();
            
            foreach ($mangas as $mangaData) {
                $manga = new Manga();
                $manga->setMangaDexId($mangaData['id']);
                $manga->setTitle($mangaData['attributes']['title']['en'] ?? array_values($mangaData['attributes']['title'])[0]);
                $manga->setDescription($mangaData['attributes']['description']['en'] ?? null);
                $manga->setStatus($mangaData['attributes']['status'] ?? null);
                $manga->setYear($mangaData['attributes']['year'] ?? null);

                // Gestion de la cover
                $coverFile = array_filter($mangaData['relationships'], fn($rel) => $rel['type'] === 'cover_art');
                if (!empty($coverFile)) {
                    $coverFile = reset($coverFile);
                    $manga->setCoverImage($coverFile['attributes']['fileName'] ?? null);
                }

                $this->entityManager->persist($manga);
            }

            $this->entityManager->flush();
            
            $io->success(sprintf('Importation réussie de %d mangas', count($mangas)));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur lors de l\'importation : ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
} 