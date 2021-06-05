<?php


namespace App\Component;


use App\Entity\Crew;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Entity\Leaderboard;
use App\Entity\Game;
use App\Entity\Prediction;
use Doctrine\ORM\EntityManagerInterface;

/**
 * La classe LeaderBoardManager implémente le service de gestion des classements.
 * Il est responsable du calcul et de la mise à jour des classements à partir
 * des résultats entrés
 *
 * @author Sébastien ZINS
 * @todo Remplacer les constantes par des paramètres applicatifs injectés dans
 * le service
 */
class LeaderBoardManager {

    /**
     *
     * @var EntityManagerInterface|EntityManager Le gestionnaire d'entités Doctrine
     */
    private $manager;
    
    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }
    
    /**
     * Initialise le classement : supprime toutes les entrées existantes, et 
     * crée de nouvelles entrées vides.
     */
    public function initLeaderboard() {
        $items = $this->manager->getRepository(Leaderboard::class)->findAll();
        foreach ($items as $item) {
            $this->manager->remove($item);
        }

        $this->manager->flush();

        // Nettoyage du manager pour évite qu'il ne s'emmêle les pinceaux entre
        // les vieilles entitiés gérées (et supprimées) et les nouvelles 
        // créées.
        //cf. https://stackoverflow.com/questions/18215975/doctrine-a-new-entity-was-found-through-the-relationship
        $this->manager->clear();
        
        $users = $this->manager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $lbItem = new Leaderboard();
            $lbItem->setPoints(0);
            $lbItem->setUser($user);
            $this->manager->persist($lbItem);
            $this->manager->flush();
        }
    }
    
    /**
     * Recalcul complet de l'ensemble des classements pour la compétition.
     */
    public function computeLeaderboards() {
        //Classement général
        $this->computeLeaderboard();
        
        //Classement par équipe
        $this->computeCrewLeaderboard();
    }
    
    
    
    /**
     * Recalcule l'ensemble du classement général à partir de toutes les rencontres
     * et pronostics saisis. Cette méthode est accessible depuis la ligne de 
     * commande wcpc:leaderboard:compute
     */
    public function computeLeaderboard() {
        $this->initLeaderboard();
        $games = $this->manager->getRepository(Game::class)
                ->findForLeaderboardCalculation();
        
        $leaderboard = $this->manager->getRepository(Leaderboard::class)
                ->findIndexedByUserIdArray();
        
        echo "Nombre de rencontres prises en compte pour le recalcul : " . count($games) . "\n";
        
        foreach($games as $game) {
            echo "Prise en compte de la rencontre $game \n";
            foreach ($game->getPredictions() as $prediction) {
                echo "  - Traitement de la prédiction " .  $prediction . "\n";
                $points = $this->computePointsForPrediction($game, $prediction);
                echo "    - Points gagnés : " . $points . "\n";
                $userLb = $leaderboard[$prediction->getUser()->getId()];
                $userLb->addPoints($points);
                $prediction->setPoints($points);
            }
        }
        
        $this->manager->flush();
    }
    
    
    /**
     * Réinitialise le classement
     */
    public function initCrewLeaderboard() {
        $crews = $this->manager->getRepository(Crew::class)->findAll();
        foreach ($crews as $crew) {
            $crew->setPoints(0);
        }
        
        $this->manager->flush();
    }
    
    /**
     * Recalcule le classement par équipe. Le classement par équipe est calculé
     * en réalisant la moyenne des points obtenus par l'ensemble des membres 
     * de l'équipe. 
     */
    public function computeCrewLeaderboard() {

        echo "Calcul des points par équipes \n";
        $this->initCrewLeaderboard();
        
        //Récupération du classement général
        $leaderboard = $this->manager->getRepository(Leaderboard::class)
                ->findAll();
        
        /* @var $lbItem Leaderboard */
        $crewPoints = [];
        foreach ($leaderboard as $lbItem) {
            $crew = $lbItem->getUser()->getCrew();
            if (!array_key_exists($crew->getName(), $crewPoints)) {
                $crewPoints[$crew->getName()] = 0;
            }
            $crewPoints[$crew->getName()] += $lbItem->getPoints();
        }
        
        $crews = $this->manager->getRepository(Leaderboard::class)->findAll();
        /* @var $crew \WCPC2K18Bundle\Entity\Crew */
        foreach ($crews as $crew) {
            echo "Points de l'équipe " . $crew->getName() . " : " . $crewPoints[$crew->getName()] . " (" . count($crew->getUsers()) . " utilisateurs)";
            $finalPoints = $crewPoints[$crew->getName()] / count($crew->getUsers());
            echo "Moyenne calculée : " . $finalPoints . "\n";
            $crew->setPoints($finalPoints);
        }
        
        $this->manager->flush();
    }
    
    /**
     * Calcul les points gagné pour une prédiction de rencontre
     * 
     * @param Game $game
     * @param Prediction $prediction
     */
    private function computePointsForPrediction(Game $game, Prediction $prediction) {
        $points = 0;
        
        if ($prediction->isPerfectlyAccurate()) {
            $points = $game->getRule()->getPointsForPerfect();
        }
        elseif ($prediction->isGoalAverageAccurate()) {
            $points = $game->getRule()->getPointsForCorrectGA();
        }
        elseif ($prediction->isWinnerAccurate()) {
            $points = $game->getRule()->getPointsForCorrectWinner();
        }
        
        //Application du coefficient multiplicateur si l'utilisateur a joué
        // un jackpot et que l'application de ce jackpot est possible
        if ($game->getRule()->isJackpotEnabled() && $prediction->getJackpot()) {
            $points *= $game->getRule()->getJackpotMultiplicator();
        }
        
        return $points;
    }

}
