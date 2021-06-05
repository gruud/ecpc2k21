<?php

namespace App\Command;

use App\Entity\Crew;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCrewCommand extends Command {

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        parent::__construct();
        $this->manager = $manager;
    }

    public function configure() {
        $this->setName('ecpc:crew:create')
            ->setDescription("Crée une nouvelle équipe")
            ->addArgument('name', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $crewName = $input->getArgument('name');

        if ($this->manager->getRepository(Crew::class)->count(['name' => $crewName])) {
            throw new \Exception("Une équipe de même nom existe déjà");
        }

        $crew = new Crew();
        $crew->setName($crewName);
        $this->manager->persist($crew);
        $this->manager->flush();
        $output->writeln('<comment>Equipe ' . $crew->getName() . ' créée!</comment>');

        return Command::SUCCESS;

    }

}