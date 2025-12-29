<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('test1@example.com');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword(
            $this->passwordHasher->hashPassword($user1, 'password1')
        );
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('test2@example.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'password2')
        );
        $manager->persist($user2);

        $manager->flush();
    }
}
