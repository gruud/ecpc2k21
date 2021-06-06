<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * La classe Prediction implémente le pronostic d'un joueur pour une rencontre
 *
 * @author Sébastien ZINS
 * 
 * @ORM\Entity(repositoryClass="App\Repository\PredictionRepository")
 * @ORM\Table(name="ecpc_predictions")
 */
class Prediction {
    
    /**
     * Validité de la prédiction : La prédiction est fausse (mauvais résultat)
     */
    const ACCURACY_NONE = 0;
            
    /**
     * Validité de la prédiction : Le résultat est trouvé
     */
    const ACCURACY_WINNER = 1;
    
    /**
     * Validité de la prédiction : La différence de buts est trouvée
     */
    const RESULT_GA = 2;
    
    /**
     * Validité de la prédiction : Score exact
     */
    const RESULT_PERFECT  =2;
    
    
    /**
     *
     * @var integer L'identifiant unique du pronostic
     * 
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     *
     * @var integer Le nombre de buts pronostiqués pour l'équipe à domicile
     * 
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer", message="Saisissez un nombre entier supérieur à zéro")
     * @Assert\Range(min="0", max="100", minMessage="Le score doit être supérieur à 0", maxMessage="Le score doit être inférieur à 100")
     */
    private $goalsHome;
    
    /**
     *
     * @var integer Le nombre de buts pronostiqués pour l'équipe à l'extérieur
     * 
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer", message="Saisissez un nombre entier supérieur à zéro")
     * @Assert\Range(min="0", max="100", minMessage="Le score doit être supérieur à 0", maxMessage="Le score doit être inférieur à 100")
     */
    private $goalsAway;
    
    /**
     *
     * @var boolean Indique si le marqueur de bonus a été positionné pour le 
     * pronostic ou non
     * 
     * @ORM\Column(type="boolean")
     */
    private $jackpot = false;
    
    /**
     *
     * @var integer Les points gagnés pour cette rencontre. Calculé lors de la
     * mise à jour des classements. Par défaut, le nombre de points est négatif
     * pour indiquer que les points n'ont pas encore été attribués
     * 
     * @ORM\Column(type="integer")
     */
    private $points = -1;
    
    /**
     *
     * @var User L'utilisateur réalisant le pronostic
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="predictions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     *
     * @var Game La rencontre pour laquelle le pronostic est réalisé
     * 
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="predictions")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;
    
    
    /**
     * Constructeur
     */
    public function __construct() {
        
    }
    
    /**
     * Récupère l'identifiant unique du pronostic
     * 
     * @return integer L'identifiant unique du pronostic
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @return int
     */
    public function getGoalsHome() {
        return $this->goalsHome;
    }

    public function getGoalsAway() {
        return $this->goalsAway;
    }

    public function getJackpot() {
        return $this->jackpot;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function getGame(): Game {
        return $this->game;
    }
    
    /**
     * Récupère les points gagnés grâce à ce pronostic
     * @return integer Les points gagnés via ce pronostic
     */
    public function getPoints() {
        return $this->points;
    }
    
    
    /**
     * Renvoie le résultat de la prédiction. Le résultat entier emploie les
     * même conventions que les résultats de la rencontre pour permettre un 
     * comparatif facile.
     * 
     * @return int entier représentant le résultat de la prédiction (1 N 2)
     */
    public function getResult() {
        
        if ($this->getGoalsHome() == $this->getGoalsAway()) {
            return Game::RESULT_DRAW;
        } elseif($this->getGoalsHome() > $this->getGoalsAway()) {
            return Game::RESULT_WINNER_HOME;
        } else {
            return Game::RESULT_WINNER_AWAY;
        }
    }
    
    /**
     * Récupère la différence de buts pronostiquée par l'utilisateur
     * 
     * @return integer La différence de buts pronostiquée
     */
    public function getGA() {
        return $this->goalsHome - $this->goalsAway;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setGoalsHome($goalsHome) {
        $this->goalsHome = $goalsHome;
    }

    public function setGoalsAway($goalsAway) {
        $this->goalsAway = $goalsAway;
    }

    public function setJackpot($jackpot) {
        $this->jackpot = $jackpot;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function setGame(Game $game) {
        $this->game = $game;
    }
    
    /**
     * Indique si le pronostic indique le bon vainqueur de la rencontre
     * 
     * @return boolean VRAI si le pronostic indique le bon vainqueur, faux sinon
     */
    public function isWinnerAccurate() {
        return $this->getResult() == $this->game->getResult();
    }   
    
    /**
     * Indique si le pronostic indique le bon vainqueur et la bonne différence
     * de buts
     * 
     * @return  boolean VRAI si le pronostic indique le bon vainqueur et la 
     * bonne différence de buts, FAUX sinon
     */
    public function isGoalAverageAccurate() {
        return $this->game->getGA() == $this->getGA();
    }
    
    /**
     * Indique si le pronostic est parfaitement exact
     * @return bool
     */
    public function isPerfectlyAccurate() {
        return ($this->game->getGoalsHome() == $this->getGoalsHome() &&
                $this->game->getGoalsAway() == $this->getGoalsAway());
    }
    
 
    
    /**
     * Positionne les points gagnés via ce pronostic
     * @param integer $points Les points à positionner
     */
    public function setPoints($points) {
        $this->points = $points;
    }
    
    public function __toString() {
        return $this->user . " : " . $this->goalsHome . " - " . $this->goalsAway;
    }
}
