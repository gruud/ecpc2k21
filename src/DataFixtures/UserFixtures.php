<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture {

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager) {
        $user = new User();
        $user->setFirstName('Jean');
        $user->setLastName("VALJEAN");
        $user->setEmail('jean.valjean@test.fr');
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'azerty01'
        ));

        $manager->persist($user);

        $manager->flush();
    }
}
