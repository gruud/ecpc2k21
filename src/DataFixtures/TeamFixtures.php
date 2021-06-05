<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Team;

/**
 * Description of LoadTeamData
 *
 * @author seb
 */
class TeamFixtures extends Fixture implements OrderedFixtureInterface {
    
    
    public function load(ObjectManager $manager) {
        
        $datafile = __DIR__ .  "/data/teams.json";
        $teams = json_decode(file_get_contents($datafile), true);
        
        foreach ($teams as $teamData) {
            $team = new Team();
            $team->setId($teamData["id"]);
            $team->setName($teamData["name"]);
            $team->setAbbreviation($teamData["abbr"]);
            
            $this->addReference("team-" . $team->getId(), $team);
            
            $manager->persist($team);
        }
        
        $manager->flush();
    }
    
    public function getOrder() {
        return 1;
    }
}
