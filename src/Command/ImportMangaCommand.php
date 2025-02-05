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
use Doctrine\DBAL\Connection;

#[AsCommand(
    name: 'app:import-manga',
    description: 'Importe les mangas depuis MangaDex API'
)]
class ImportMangaCommand extends Command
{
    public function __construct(
        private readonly MangaDexApiService $mangaDexApi,
        private readonly EntityManagerInterface $entityManager,
        private readonly Connection $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->info('Début de l\'importation des mangas...');
        
        try {
            // Désactiver les contraintes de clé étrangère
            $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');

            // Supprimer les données liées
            $this->connection->executeStatement('DELETE FROM favorite WHERE manga_id IS NOT NULL');
            $this->connection->executeStatement('DELETE FROM review WHERE manga_id IS NOT NULL');
            $this->connection->executeStatement('TRUNCATE TABLE manga');

            // Réactiver les contraintes de clé étrangère
            $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
            
            $result = $this->mangaDexApi->getMangaList();
            $mangas = $result['data'];
            
            foreach ($mangas as $mangaData) {
                if (!isset($mangaData['id'])) {
                    $io->warning('Manga sans ID trouvé : ' . json_encode($mangaData));
                    continue;
                }

                // Vérifier si le manga existe déjà
                $existingManga = $this->entityManager->getRepository(Manga::class)
                    ->findOneBy(['mangaDexId' => $mangaData['id']]);
                
                if ($existingManga) {
                    continue; // Sauter ce manga s'il existe déjà
                }

                $manga = new Manga();
                $manga->setMangaDexId($mangaData['id']);
                
                if (!isset($mangaData['attributes']['title'])) {
                    $io->warning('Manga sans titre trouvé : ' . json_encode($mangaData));
                    continue;
                }

                // Gestion du titre avec vérification
                $title = $mangaData['attributes']['title']['en'] 
                    ?? array_values($mangaData['attributes']['title'])[0] 
                    ?? 'Titre inconnu';
                $manga->setTitle($title);
                
                // Gestion de la description avec vérification
                $description = isset($mangaData['attributes']['description']) 
                    ? ($mangaData['attributes']['description']['en'] 
                        ?? array_values($mangaData['attributes']['description'])[0] 
                        ?? null)
                    : null;
                $manga->setDescription($description);
                
                $manga->setStatus($mangaData['attributes']['status'] ?? null);
                $manga->setYear($mangaData['attributes']['year'] ?? null);

                // Gestion de la cover avec vérification
                $coverFile = null;
                if (isset($mangaData['relationships']) && is_array($mangaData['relationships'])) {
                    foreach ($mangaData['relationships'] as $relationship) {
                        if (isset($relationship['type']) 
                            && $relationship['type'] === 'cover_art' 
                            && isset($relationship['attributes']['fileName'])) {
                            $coverFile = $relationship['attributes']['fileName'];
                            break;
                        }
                    }
                }
                $manga->setCoverImage($coverFile);

                $this->entityManager->persist($manga);
            }

            $this->entityManager->flush();
            
            $io->success(sprintf('Importation réussie de %d mangas', count($mangas)));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            // En cas d'erreur, s'assurer que les contraintes sont réactivées
            $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
            
            $io->error('Erreur lors de l\'importation : ' . $e->getMessage());
            $io->error('Trace : ' . $e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
} 