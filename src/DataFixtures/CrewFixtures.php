<?php

namespace App\DataFixtures;

use App\Entity\Crew;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CrewFixtures extends Fixture implements OrderedFixtureInterface {

    public function __construct() {
    }

    public function load(ObjectManager $manager) {
        $datafile = __DIR__ .  "/data/crews.json";
        $crews = json_decode(file_get_contents($datafile), true);

        foreach ($crews as $crewData) {
            $crew = new Crew();
            $crew->setName($crewData['label']);
            $this->addReference('crew-' . $crew->getName(), $crew);
            $manager->persist($crew);
            $manager->flush();
        }


    }

    public function getOrder() {
        return 1;
    }
}