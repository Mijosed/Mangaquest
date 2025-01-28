<?php

namespace App\Command;

use App\Service\AniListApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\AnimeRepository;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'app:fetch-anime',
    description: 'Fetch anime data from AniList API',
)]
class FetchAnimeCommand extends Command
{
    public function __construct(
        private readonly AniListApiService $aniListApi,
        private readonly EntityManagerInterface $entityManager,
        private readonly AnimeRepository $animeRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('page', 'p', InputOption::VALUE_OPTIONAL, 'Page number to fetch', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $page = $input->getOption('page');

        $io->info(sprintf('Fetching page %d of anime data from AniList...', $page));

        try {
            $animeData = $this->aniListApi->fetchPopularAnime($page, 10);
            
            $newTitles = array_map(fn($data) => $data['title']['romaji'], $animeData);
            
            $existingAnimes = $this->animeRepository->findByTitles($newTitles);
            $existingTitles = array_map(fn($anime) => $anime->getTitle(), $existingAnimes);
            
            $newCount = 0;
            $skipCount = 0;
            
            foreach ($animeData as $data) {
                $title = $data['title']['romaji'];
                
                if (in_array($title, $existingTitles)) {
                    $skipCount++;
                    $io->text(sprintf('Ignoré : %s (déjà existant)', $title));
                    continue;
                }
                
                $anime = $this->aniListApi->mapToAnimeEntity($data);
                $this->entityManager->persist($anime);
                $newCount++;
                $io->text(sprintf('Ajouté : %s', $title));
            }
            
            $this->entityManager->flush();
            
            $io->success(sprintf(
                'Import de la page %d terminé : %d nouveaux animes ajoutés, %d animes ignorés car déjà existants.',
                $page,
                $newCount,
                $skipCount
            ));

            if ($newCount > 0) {
                $io->info(sprintf(
                    'Pour charger la page suivante, exécutez : php bin/console app:fetch-anime --page=%d',
                    $page + 1
                ));
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Une erreur est survenue : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 