<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Organizer;
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
            'Concert de Jazz' => ['music', 'culture'],
            'Exposition d\'Art Moderne' => ['culture', 'art'],
            'Marathon de Paris' => ['sport', 'outdoor'],
            'Festival de Gastronomie' => ['food', 'culture'],
            'Conférence Tech' => ['technology', 'business'],
            'Tournoi de Football' => ['sport', 'outdoor'],
            'Soirée Théâtre' => ['culture', 'art']
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

        // Création des organisateurs
        $organizers = [];
        $organizations = [
            'EventPro Paris',
            'Sport & Culture Organisation',
            'Tech Conference France',
            'Arts et Spectacles Production',
            'Festival Management'
        ];

        for ($i = 0; $i < 5; $i++) {
            $organizer = new Organizer();
            $organizer->setEmail($faker->email())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPassword('password123')
                ->setOrganization($organizations[$i])
                ->setPhone($faker->phoneNumber())
                ->setBirthDate($faker->dateTimeBetween('-50 years', '-25 years'));

            $manager->persist($organizer);
            $organizers[] = $organizer;
        }

        foreach ($eventTitles as $title => $categories) {
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

            // Ajout de 1 à 2 organisateurs aléatoires
            $organizersCount = rand(1, 2);
            $shuffledOrganizers = $organizers;
            shuffle($shuffledOrganizers);

            for ($i = 0; $i < $organizersCount; $i++) {
                $event->addOrganizer($shuffledOrganizers[$i]);
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