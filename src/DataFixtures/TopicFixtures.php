<?php

namespace App\DataFixtures;

use App\Entity\Topic;
use App\Entity\MangaTopic;
use App\Entity\AnimeTopic;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TopicFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Tableaux de titres pour plus de cohérence
        $mangaTitles = ['One Piece', 'Naruto', 'Dragon Ball', 'Demon Slayer', 'Jujutsu Kaisen', 'My Hero Academia'];
        $animeTitles = ['L\'Attaque des Titans', 'Death Note', 'Fullmetal Alchemist', 'Hunter x Hunter', 'Sword Art Online', 'Tokyo Ghoul'];

        // Création de topics manga approuvés
        for ($i = 0; $i < 5; $i++) {
            $topic = new MangaTopic();
            $mangaTitle = $faker->randomElement($mangaTitles);
            $createdAt = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now'));
            
            $topic->setTitle("Discussion {$mangaTitle} - Chapitre " . $faker->numberBetween(1, 1000))
                  ->setContent($faker->paragraphs(3, true))
                  ->setAuthor($this->getRandomUser($manager))
                  ->setCreatedAt($createdAt)
                  ->setViews($faker->numberBetween(10, 1000))
                  ->setIsApproved(true)
                  ->setImageFilename('defaultImage.png')
                  ->setMangaTitle($mangaTitle)
                  ->setChapter($faker->numberBetween(1, 1000));
            
            $manager->persist($topic);
            $this->addReference('manga_topic_' . $i, $topic);

            // Création de posts pour ce topic
            $this->createPosts($manager, $topic, $faker);
        }

        // Création de topics anime approuvés
        for ($i = 0; $i < 5; $i++) {
            $topic = new AnimeTopic();
            $animeTitle = $faker->randomElement($animeTitles);
            $createdAt = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now'));

            $topic->setTitle("Discussion {$animeTitle} - Épisode " . $faker->numberBetween(1, 24))
                  ->setContent($faker->paragraphs(3, true))
                  ->setAuthor($this->getRandomUser($manager))
                  ->setCreatedAt($createdAt)
                  ->setViews($faker->numberBetween(10, 1000))
                  ->setIsApproved(true)
                  ->setImageFilename('defaultImage.png')
                  ->setAnimeTitle($animeTitle)
                  ->setEpisode($faker->numberBetween(1, 24));
            
            $manager->persist($topic);
            $this->addReference('anime_topic_' . $i, $topic);

            // Création de posts pour ce topic
            $this->createPosts($manager, $topic, $faker);
        }

        // Création de topics généraux approuvés
        for ($i = 0; $i < 5; $i++) {
            $topic = new Topic();
            $createdAt = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now'));

            $topic->setTitle($faker->sentence(6))
                  ->setContent($faker->paragraphs(3, true))
                  ->setAuthor($this->getRandomUser($manager))
                  ->setCreatedAt($createdAt)
                  ->setViews($faker->numberBetween(10, 1000))
                  ->setIsApproved(true)
                  ->setImageFilename('defaultImage.png');
            
            $manager->persist($topic);
            $this->addReference('general_topic_' . $i, $topic);

            // Création de posts pour ce topic
            $this->createPosts($manager, $topic, $faker);
        }

        // Topics en attente de validation
        $this->createPendingTopics($manager, $faker);

        $manager->flush();
    }

    private function createPosts(ObjectManager $manager, Topic $topic, \Faker\Generator $faker): void
    {
        $numPosts = $faker->numberBetween(3, 10);
        for ($i = 0; $i < $numPosts; $i++) {
            $post = new Post();
            $topicDate = $topic->getCreatedAt()->format('Y-m-d H:i:s');
            $postDate = $faker->dateTimeBetween($topicDate, 'now');
            $createdAt = \DateTimeImmutable::createFromMutable($postDate);

            $post->setContent($faker->paragraphs(2, true))
                 ->setAuthor($this->getRandomUser($manager))
                 ->setTopic($topic)
                 ->setCreatedAt($createdAt);
            
            $manager->persist($post);
        }
    }

    private function createPendingTopics(ObjectManager $manager, \Faker\Generator $faker): void
    {
        // Topics manga en attente
        for ($i = 0; $i < 2; $i++) {
            $topic = new MangaTopic();
            $createdAt = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', 'now'));

            $topic->setTitle("Nouveau chapitre de " . $faker->randomElement(['Chainsaw Man', 'Black Clover', 'Bleach', 'One Punch Man']))
                  ->setContent($faker->paragraphs(3, true))
                  ->setAuthor($this->getRandomUser($manager))
                  ->setCreatedAt($createdAt)
                  ->setViews($faker->numberBetween(1, 50))
                  ->setIsApproved(false)
                  ->setImageFilename('defaultImage.png')
                  ->setMangaTitle($faker->randomElement(['Chainsaw Man', 'Black Clover', 'Bleach', 'One Punch Man']))
                  ->setChapter($faker->numberBetween(1, 200));
            
            $manager->persist($topic);
        }

        // Topics anime en attente
        for ($i = 0; $i < 2; $i++) {
            $topic = new AnimeTopic();
            $createdAt = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', 'now'));

            $topic->setTitle("Nouvel épisode de " . $faker->randomElement(['Demon Slayer', 'Dr. Stone', 'The Promised Neverland', 'Spy x Family']))
                  ->setContent($faker->paragraphs(3, true))
                  ->setAuthor($this->getRandomUser($manager))
                  ->setCreatedAt($createdAt)
                  ->setViews($faker->numberBetween(1, 50))
                  ->setIsApproved(false)
                  ->setImageFilename('defaultImage.png')
                  ->setAnimeTitle($faker->randomElement(['Demon Slayer', 'Dr. Stone', 'The Promised Neverland', 'Spy x Family']))
                  ->setEpisode($faker->numberBetween(1, 12));
            
            $manager->persist($topic);
        }
    }

    private function getRandomUser(ObjectManager $manager): User
    {
        $users = $manager->getRepository(User::class)->findAll();
        return $users[array_rand($users)];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
} 