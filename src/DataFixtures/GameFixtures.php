<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\Entity\Game;
use Doctrine\Persistence\ObjectManager;

/**
 * La classe LoadTeamData implémente la méthode de chargement initiale des
 * rencontres de la compétition, réalisée à partir d'un fichier JSON. 
 *
 * @author seb
 */
class GameFixtures extends Fixture implements OrderedFixtureInterface {
    
    
    public function load(ObjectManager $manager) {
        
        $datafile = __DIR__ .  "/data/games.json";
        $games = json_decode(file_get_contents($datafile), true);
        
        foreach ($games as $gameData) {
            $game = new Game();
            $game->setId($gameData['id']);
            $game->setType($gameData['type']);
            $game->setHomeTeam($this->getReference('team-' . $gameData['homeTeam']));
            $game->setAwayTeam($this->getReference('team-' . $gameData['awayTeam']));
            $game->setKickoff(\DateTime::createFromFormat("d/m/Y H:i", $gameData['date']));
            $game->setPhase($gameData["phase"]);
            $game->setGroup($gameData["group"]);
            $game->setRule($this->getReference('rule-' . $gameData["rules"]));
            
            $manager->persist($game);
        }
        
        $manager->flush();
    }
    
    public function getOrder() {
        return 4;
    }
}
