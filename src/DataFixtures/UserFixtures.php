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
                ->setAvatar($this->faker->imageUrl(640, 480, 'people'));

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
                ->setAvatar($this->faker->imageUrl(640, 480, 'people'));

            $manager->persist($user);
            $this->addReference('user_random_' . $i, $user);
        }

        $manager->flush();
    }
}