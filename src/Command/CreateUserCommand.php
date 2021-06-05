<?php

namespace App\Command;

use App\Entity\Crew;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Runner\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * Description of CreateUser
 *
 * @author seb
 */
class CreateUserCommand extends Command {

    /**
     * @var EntityManagerInterface | EntityManager
     */
    private $manager;

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher) {
        parent::__construct();
        $this->manager = $manager;
        $this->passwordHasher = $passwordHasher;
    }

    public function configure() {
        $this->setName('ecpc:user:create')
                ->setDescription("Crée un nouvel utilisateur")
                ->addArgument('username', InputArgument::REQUIRED)
                ->addArgument('password', InputArgument::REQUIRED)
                ->addArgument('firstName', InputArgument::REQUIRED)
                ->addArgument('lastName', InputArgument::REQUIRED)
                ->addArgument('department', InputArgument::REQUIRED)
                ->addArgument('crew', InputArgument::REQUIRED)
                ->addOption(
                    'admin',
                    null,
                    InputOption::VALUE_NONE,
                    "Positionne l'utilisateur comme administrateur"
                );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        
        $user = new User();
        $crewName = $input->getArgument('crew');

        $crew = $this->manager->getRepository(Crew::class)->findOneBy(['name' => $crewName]);
        if (null === $crew) {
            throw new Exception("L'équipe " . $crewName . "n'existe pas");
        }

        $user->setUsername($input->getArgument('username'));
        $user->setPassword($this->passwordHasher->hashPassword($user, $input->getArgument('password')));
        $user->setFirstName($input->getArgument('firstName'));
        $user->setLastName($input->getArgument('lastName'));
        $user->setDepartment($input->getArgument('department'));
        $user->setCrew($crew);
        if ($input->hasOption('admin')) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->manager->persist($user);
        $this->manager->flush();

        return 0;
    }

}
