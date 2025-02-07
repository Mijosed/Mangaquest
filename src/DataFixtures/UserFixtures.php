<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $faker;
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->passwordHasher = $passwordHasher;
    }

    private function getRandomAvatar(): string
    {
        static $count = 0;
        $count++;
        
        // Utiliser un seed incrémental au lieu d'un aléatoire pour garantir des avatars uniques
        return "https://api.dicebear.com/7.x/avataaars/svg?seed=avatar{$count}";
    }

    public function load(ObjectManager $manager): void
    {
        // Création des utilisateurs spécifiques
        $predefinedUsers = [
            [
                'email' => 'admin@gmail.com',
                'roles' => ['ROLE_ADMIN']
            ],
            [
                'email' => 'banned@gmail.com',
                'roles' => ['ROLE_BANNED']
            ],
            [
                'email' => 'user@gmail.com',
                'roles' => ['ROLE_USER']
            ]
        ];

        foreach ($predefinedUsers as $userData) {
            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'test');

            $user->setEmail($userData['email'])
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setPassword($hashedPassword)
                ->setRoles($userData['roles'])
                ->setBirthDate($this->faker->dateTimeBetween('-50 years', '-18 years'))
                ->setIsSubscribedToNewsletter($this->faker->boolean())
                ->setPreferences($this->faker->randomElement(['sport', 'culture', 'music', 'technology']))
                ->setAvatar($this->getRandomAvatar());

            $manager->persist($user);
            $this->addReference('user_' . $userData['email'], $user);
        }

        // Création d'utilisateurs supplémentaires
        for ($i = 0; $i < 7; $i++) {
            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'test');

            $user->setEmail($this->faker->email())
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setPassword($hashedPassword)
                ->setBirthDate($this->faker->dateTimeBetween('-50 years', '-18 years'))
                ->setIsSubscribedToNewsletter($this->faker->boolean())
                ->setPreferences($this->faker->randomElement(['sport', 'culture', 'music', 'technology']))
                ->setAvatar($this->getRandomAvatar());

            $manager->persist($user);
            $this->addReference('user_random_' . $i, $user);
        }

        $manager->flush();
    }

    public static function loadFixtures(ObjectManager $manager, array $users): void
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

        foreach ($eventTitles as $index => $title) {
            $event = new self();

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
}