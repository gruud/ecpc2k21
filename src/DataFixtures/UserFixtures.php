<?php

namespace App\DataFixtures;

use App\Entity\Crew;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface {

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }


    /**
     * Ajout de l'utilisateur d'administration
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $datafile = __DIR__ .  "/data/users.json";
        $users = json_decode(file_get_contents($datafile), true);

        foreach ($users as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setDepartment($userData['department']);

            /** @var Crew $crew */
            $crew = $this->getReference('crew-' . $userData['crew']);
            $user->setCrew($crew);

            if ($userData['admin']) {
                $user->setRoles(['ROLE_ADMIN']);
            }

            $manager->persist($user);
            $manager->flush();


        }
    }

    public function getOrder() {
        return 2;
    }
}
