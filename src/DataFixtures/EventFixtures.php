<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $eventTitles = [
            'Concert de Jazz',
            'Exposition d\'Art Moderne',
            'Marathon de Paris',
            'Festival de Gastronomie',
            'Conférence Tech',
            'Tournoi de Football',
            'Soirée Théâtre'
        ];

        $locations = [
            'Palais des Congrès, Paris',
            'Stade de France, Saint-Denis',
            'Zénith de Paris',
            'Centre Pompidou',
            'Parc des Princes',
            'AccorHotels Arena'
        ];

        // Récupération des références des utilisateurs
        $users = [];
        $users[] = $this->getReference('user_admin@gmail.com');
        $users[] = $this->getReference('user_user@gmail.com');

        for ($i = 0; $i < 7; $i++) {
            $users[] = $this->getReference('user_random_' . $i);
        }

        foreach ($eventTitles as $title) {
            $event = new Event();

            $event->setTitle($title)
                ->setDate($faker->dateTimeBetween('now', '+6 months'))
                ->setLocation($faker->randomElement($locations))
                ->setDescription($faker->paragraphs(2, true))
                ->setCreator($users[array_rand($users)]);

            // Ajout de 3 à 8 participants aléatoires
            $participantsCount = rand(3, 8);
            $shuffledUsers = $users;
            shuffle($shuffledUsers);

            for ($i = 0; $i < $participantsCount && $i < count($shuffledUsers); $i++) {
                $event->addParticipant($shuffledUsers[$i]);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}