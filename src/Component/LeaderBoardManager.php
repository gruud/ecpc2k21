<?php


namespace App\Component;

use App\Entity\Crew;
use App\Entity\CrewLeaderboard;
use App\Entity\User;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use App\Entity\Leaderboard;
use App\Entity\Game;
use App\Entity\Prediction;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Xml\Exception;

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
        
        foreach($games as $game) {
            foreach ($game->getPredictions() as $prediction) {
                $points = $this->computePointsForPrediction($game, $prediction, $leaderboard[$prediction->getUser()->getId()]);
                $userLb = $leaderboard[$prediction->getUser()->getId()];
                $userLb->addPoints($points);
                $prediction->setPoints($points);
            }
        }
        
        $this->manager->flush();
    }
    
    
    /**
     * Réinitialise entièrement le classement par équipe en supprimant toutes les
     * données
     */
    public function initCrewLeaderboard() {
        $crewLeaderboard = $this->manager->getRepository(CrewLeaderboard::class)->findAll();
        foreach ($crewLeaderboard as $citem) {
            $this->manager->remove($citem);
        }
        
        $this->manager->flush();

        $crews = $this->manager->getRepository(Crew::class)->findAll();
        foreach($crews as $crew) {
            $clb = new CrewLeaderboard();
            $clb->setCrew($crew);
            $clb->setPoints(0.0);
        }
    }
    
    /**
     * Recalcule le classement par équipe. Le classement par équipe est calculé
     * en réalisant la moyenne des points obtenus par l'ensemble des membres 
     * de l'équipe. 
     */
    public function computeCrewLeaderboard() {

        $this->initCrewLeaderboard();

        $leaderboard = $this->manager
            ->getRepository(Leaderboard::class)
            ->findForCrewLeaderboardCalculation();



        foreach ($leaderboard as $lbItem) {
            $avg = $lbItem['avg'];
            $crew = $this->manager->getRepository(Crew::class)->findOneByName($lbItem['name']);
            $crewLb = new CrewLeaderboard();
            $crewLb->setPoints($avg);
            $crewLb->setCrew($crew);
            $crew->setLeaderboard($crewLb);
            $this->manager->persist($crewLb);
        }

        $this->manager->flush();

    }
    
    /**
     * Calcul les points gagné pour une prédiction de rencontre
     * 
     * @param Game $game
     * @param Prediction $prediction
     */
    private function computePointsForPrediction(Game $game, Prediction $prediction, Leaderboard $leaderboard) {
        $points = 0;

        $leaderboard->incrementPlayedCount();
        
        if ($prediction->isPerfectlyAccurate()) {
            $points = $game->getRule()->getPointsForPerfect();
            $leaderboard->incrementPerfectCount();

        }
        elseif ($prediction->isGoalAverageAccurate()) {
            $points = $game->getRule()->getPointsForCorrectGA();
            $leaderboard->incrementGoalAverageAccurateCount();
        }
        elseif ($prediction->isWinnerAccurate()) {
            $points = $game->getRule()->getPointsForCorrectWinner();
            $leaderboard->incrementWinnerAccurateCount();
        }
        
        //Application du coefficient multiplicateur si l'utilisateur a joué
        // un jackpot et que l'application de ce jackpot est possible

        if ($game->getRule()->isJackpotEnabled() && $prediction->getJackpot()) {
            $points *= $game->getRule()->getJackpotMultiplicator();
            $leaderboard->incrementJackpotPlayedCount();
            if ($points > 0) {
                $leaderboard->incrementJackpotPointsCount();
            }
        }

        if ($points > 0) {
            $leaderboard->incrementWinCount();
        } else {
            $leaderboard->incrementLoseCount();
        }
        
        return $points;
    }
}
