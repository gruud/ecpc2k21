<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * La classe Leaderboard implémente le classement d'un utilisateur pour le 
 * concours de pronostic. Ce classement comporte un nombre de points calculés
 * à partir des pronostics effectués par le joueur, ainsi que divers autres
 * classements (moyenne de point, nombre de résultats corrects réalisés, etc). 
 *
 * @author Sébastien ZINS
 * 
 * @ORM\Entity(repositoryClass="App\Repository\LeaderboardRepository")
 * @ORM\Table(name="ecpc_leaderboards")
 */
class Leaderboard {
    
    /**
     *
     * @var integer L'identifiant unique du classement
     * 
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     *
     * @var User L'utilisateur lié au classement
     * 
     * @ORM\OneToOne(targetEntity="User", inversedBy="leaderboard")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     *
     * @var integer Le nombre standard de points de l'utilisateur au classement
     * 
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @var int Le nombre de rencontres pour lequel l'utilisateur a pronostiqué
     *
     * @ORM\Column(name="played_count", type="integer")
     */
    private $playedCount = 0;

    /**
     * @var int Le nombre de rencontres où l'utilisateur a gagné des points
     *
     * @ORM\Column(name="win_count", type="integer")
     */
    private $winCount = 0;

    /**
     * @var int Le nombre de rencontres où l'utilisateur a perdu des points
     *
     * @ORM\Column(name="lose_count", type="integer")
     */
    private $loseCount = 0;

    /**
     * @var int Le nombre de pronostics où l'utilisateur a deviné le bon vainqueur
     *
     * @ORM\Column(name="winner_accurate_count", type="integer")
     */
    private $winnerAccurateCount = 0;

    /**
     * @var int Le nombre de pronostics où l'utilisateur a trouvé la bonne différence
     * de but sur la rencontre
     *
     * @ORM\Column(name="goal_average_accurate_count", type="integer")
     */
    private $goalAverageAccurateCount = 0;

    /**
     * @var int Le nombre de fois où l'utilisateur a trouve le résultat exact
     *
     * @ORM\Column(name="perfect_count", type="integer")
     */
    private $perfectCount = 0;

    /**
     * @var int Indique le nombre de prédiction ayant débouché sur l'attribution
     * de points à l'utilisateur
     *
     * @ORM\Column(name="prediction_with_points_count", type="integer")
     */
    private $predictionWithPointsCount = 0;

    /**
     * @var int Indique le nombre de fois où l'utilisateur à joué son joker
     *
     * @ORM\Column(name="jackpot_played_count", type="integer")
     */
    private $jackpotPlayedCount = 0;

    /**
     * @var int Indique le nombre de fois où l'utilisateur a gagné des points
     * en jouant son joker.
     *
     * @ORM\Column(name="jackpot_points_count", type="integer")
     */
    private $jackpotPointsCount = 0;

    /**
     * Récupère l'identifiant unique du classement
     * 
     * @return integer l'Identifiant unique du classement
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Récupère l'utilisateur rattaché au classement
     * 
     * @return User L'utilisateur rattaché au classement
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * Récupère les points du classement standard
     * 
     * @return integer Le nombre de points attribué au classement standard
     */
    public function getPoints() {
        return $this->points;
    }

    /**
     * Positionne l'utilisateur associé au classement 
     * 
     * @param User $user L'utilisateur à positionner
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * Positionner les points du classement standard
     * @param integer $points Les points du classement à positionner
     */
    public function setPoints($points) {
        $this->points = $points;
    }
    
    /**
     * Ajoute $points points aux points du classement actuellement défini
     * @param integer $points Les points à ajouter au classement
     */
    public function addPoints($points) {
        $this->points += $points;
    }

    /**
     * @return int
     */
    public function getWinnerAccurateCount(): int {
        return $this->winnerAccurateCount;
    }

    /**
     * @param int $winnerAccurateCount
     */
    public function setWinnerAccurateCount(int $winnerAccurateCount): void {
        $this->winnerAccurateCount = $winnerAccurateCount;
    }

    public function incrementWinnerAccurateCount(): void {
        $this->winnerAccurateCount++;
    }

    /**
     * @return int
     */
    public function getGoalAverageAccurateCount(): int {
        return $this->goalAverageAccurateCount;
    }

    /**
     * @param int $goalAverageAccurateCount
     */
    public function setGoalAverageAccurateCount(int $goalAverageAccurateCount): void {
        $this->goalAverageAccurateCount = $goalAverageAccurateCount;
    }

    public function incrementGoalAverageAccurateCount(): void {
        $this->goalAverageAccurateCount++;
    }

    /**
     * @return int
     */
    public function getPerfectCount(): int {
        return $this->perfectCount;
    }

    /**
     * @param int $perfectCount
     */
    public function setPerfectCount(int $perfectCount): void {
        $this->perfectCount = $perfectCount;
    }

    public function incrementPerfectCount(): void {
        $this->perfectCount++;
    }

    /**
     * @return int
     */
    public function getPredictionWithPointsCount(): int {
        return $this->predictionWithPointsCount;
    }

    /**
     * @param int $predictionWithPointsCount
     */
    public function setPredictionWithPointsCount(int $predictionWithPointsCount): void {
        $this->predictionWithPointsCount = $predictionWithPointsCount;
    }

    public function incrementPredictionWithPointsCount(): void {
        $this->predictionWithPointsCount++;
    }

    /**
     * @return int
     */
    public function getJackpotPlayedCount(): int {
        return $this->jackpotPlayedCount;
    }

    /**
     * @param int $JackpotPlayedCount
     */
    public function setJackpotPlayedCount(int $jackpotPlayedCount): void {
        $this->jackpotPlayedCount = $jackpotPlayedCount;
    }

    public function incrementJackpotPlayedCount(): void {
        $this->jackpotPlayedCount++;
    }

    /**
     * @return int
     */
    public function getJackpotPointsCount(): int {
        return $this->jackpotPointsCount;
    }

    /**
     * @param int $jackpotPointsCount
     */
    public function setJackpotPointsCount(int $jackpotPointsCount): void {
        $this->jackpotPointsCount = $jackpotPointsCount;
    }

    public function incrementJackpotPointsCount(): void {
        $this->jackpotPointsCount++;
    }

    /**
     * @return int
     */
    public function getPlayedCount(): int {
        return $this->playedCount;
    }

    /**
     * @param int $playedCount
     */
    public function setPlayedCount(int $playedCount): void {
        $this->playedCount = $playedCount;
    }

    public function incrementPlayedCount() {
        $this->playedCount++;
    }

    /**
     * @return int
     */
    public function getWinCount(): int {
        return $this->winCount;
    }

    /**
     * @param int $winCount
     */
    public function setWinCount(int $winCount): void {
        $this->winCount = $winCount;
    }

    public function incrementWinCount(): void {
        $this->winCount++;
    }

    /**
     * @return int
     */
    public function getLoseCount(): int {
        return $this->loseCount;
    }

    /**
     * @param int $loseCount
     */
    public function setLoseCount(int $loseCount): void {
        $this->loseCount = $loseCount;
    }

    public function incrementLoseCount(): void {
        $this->loseCount++;
    }
    
    /**
     * Renvoie une représentation de l'objet sous la forme d'une chaîne de 
     * caractères
     * @return string La chaîne associée à l'objet 
     */
    public function __toString() {
        return "Classement pour l'utilisateur " . $this->getUser()->getFirstName()
                . " " . strtoupper($this->getUser()->getLastName());
    }
}
