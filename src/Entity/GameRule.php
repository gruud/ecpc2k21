<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * La classe GameRule implémente la définition d'un jeu de règles à utiliser pour
 * calculer les points des joueurs à l'issue de la rencontre, lorsque les scores
 * sont calculés. 
 * 
 * Cette classe pose les fondements d'une gestion des règles réalisable via 
 * l'interface applicative par un administrateur
 *
 * @author Sébastien ZINS
 * 
 * @ORM\Entity()
 * @ORM\Table(name="ecpc_game_rules")
 */
class GameRule {
    
    /**
     *
     * @var integer L'identifiant unique  du jeu de règles
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @var string Un champ contenant le nom du jeu de règles
     * 
     * @ORM\Column(type="string", length=128)
     */
    private $label;
    
    
    /**
     *
     * @var integer Le nombre de points attribués si l'utilisateur
     * a trouvé le vainqueur
     * 
     * @ORM\Column(name="points_winner", type="integer")
     */
    private $pointsForCorrectWinner;
    
    /**
     *
     * @var integer Le nombre de points attribués si l'utilisateur troue le 
     * bon vainqueur et la bonne différence de buts
     * 
     * @ORM\Column(name="points_ga", type="integer")
     */
    private $pointsForCorrectGA;
    
    /**
     *
     * @var integer Le nombre de points attribués si l'utilisateur trouve le 
     * résultat exact de la rencontre
     * 
     * @ORM\Column(name="points_perfect", type="integer")
     */
    private $pointsForPerfect;
    
    /**
     *
     * @var boolean Indique si l'utilisateur a la possibilité de réaliser le 
     * jackpot sur la rencontre (i.e s'il peut multiplier ses points par le 
     * coefficient multiplicateur défini dans cette règle)
     * 
     * @ORM\Column(name="jackpot_enabled", type="boolean")
     */
    private $jackpotEnabled;
    
    /**
     *
     * @var integer Le coefficient multiplicateur à appliquer aux points déjà
     * gagné si l'utilisateur a joué son jackpot et si le jackpot est 
     * autorisé pour le jeu de règles en cours
     * 
     * @ORM\Column(name="jackpot_multiplicator", type="integer");
     */
    private $jackpotMultiplicator;
    
    /**
     *
     * @var ArrayCollection | Game[] La liste des parties utilisant ces règles 
     * du jeu
     * 
     * @ORM\OneToMany(targetEntity="Game", mappedBy="rule")
     */
    private $games;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->games = new ArrayCollection();
    }
    
    /**
     * Recupère l'identifiant unique de la règle du jeu
     * 
     * @return integer L'identifiant unique de la règle du jeu
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Récupère l'étiquette associée à la règle du jeu
     * 
     * @return string L'étiquette associée à la règle du jeu
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * Récupère le nombre de points attribuable en cas de vainqueur trouvé
     * 
     * @return int nombre de points
     */
    public function getPointsForCorrectWinner(): int {
        return $this->pointsForCorrectWinner;
    }
    
    /**
     * Récupère le nombre de points attribuable en cas de différence de buts
     * correcte
     * @return integer Le nombre de points
     */
    public function getPointsForCorrectGA(): int {
        return $this->pointsForCorrectGA;
    }

    /**
     * Récupère le nombre de point attribuable si l'utilisateur trouve le 
     * score exact de la rencontre
     * 
     * @return integer Le nombre de points
     */
    public function getPointsForPerfect(): int {
        return $this->pointsForPerfect;
    }

    /**
     * Indique si le jackpot est utilisable dans le cadre de cette règle
     * 
     * @return boolean VRAI si l'utilisateur peut utiliser son jackpot,
     * FAUX sinon
     */
    public function isJackpotEnabled() {
        return $this->jackpotEnabled;
    }

    /**
     * Récupère le coefficient multiplicateur à appliquer aux points en cas
     * de jackpot
     * 
     * @return integer Le coefficient multiplicateur à appliquer
     */
    public function getJackpotMultiplicator(): int {
        return $this->jackpotMultiplicator;
    }
    
    /**
     * Récupère les parties utilisant ce jeu de règles
     * 
     * @return ArrayCollection | Game[]
     */
    public function getGames() {
        return $this->games;
    }

    /**
     * Positionne l'identifiant unique de la règle
     * @param integer $id L'identifiant à positionner
     */
    public function setId(int $id) {
        $this->id = $id;
    }
    
    
    /**
     * Positionne l'étiquette à associer au jeu de règles
     * 
     * @param string $label L'étiquette à positionner
     */
    public function setLabel(string $label) {
        $this->label = $label;
    }

    /**
     * Positionne le nombre de points à attribuer à l'utilisateur s'il trouve
     * le bon vainqueur de la rencontre
     * 
     * @param integer $pointsForCorrectWinner Le nombre de points attribuables
     * à positioner
     */
    public function setPointsForCorrectWinner(int $pointsForCorrectWinner) {
        $this->pointsForCorrectWinner = $pointsForCorrectWinner;
    }

    /**
     * Positionne le nombre de points à attribuer à l'utilisateur s'il trouve
     * le résultat de la rencontre et la bonne différence de buts
     * 
     * @param integer $pointsForCorrectGA Le nombre de points attribuables à 
     * positionner
     */
    public function setPointsForCorrectGA(int $pointsForCorrectGA) {
        $this->pointsForCorrectGA = $pointsForCorrectGA;
    }

    /**
     * Positionne le nombre de points à attribuer à l'utilisateur s'il trouve
     * le score exact
     * @param int $pointsForPerfect Le nombre de points attribuables à positionner
     */
    public function setPointsForPerfect(int $pointsForPerfect) {
        $this->pointsForPerfect = $pointsForPerfect;
    }

    /**
     * Positionne l'indicateur autorisant ou non un jackpot à être joué pour la
     * rencontre
     * 
     * @param boolean $jackpotEnabled L'indicateur de jackpot à positionner
     */
    public function setJackpotEnabled(int $jackpotEnabled) {
        $this->jackpotEnabled = $jackpotEnabled;
    }

    /**
     * Positionne le coefficient multiplicateur du jackpot pour la règle en cours
     * 
     * @param integer $jackpotMultiplicator Le coefficient multiplicateur
     * à positionner
     */
    public function setJackpotMultiplicator(int $jackpotMultiplicator) {
        $this->jackpotMultiplicator = $jackpotMultiplicator;
    }
    
    /**
     * Positionne la liste des parties utilisant cette règle
     * @param ArrayCollection | Game[] $games Les parties à positionner
     */
    public function setGames(ArrayCollection $games) {
        $this->games = $games;
    }
    
    /**
     * Ajoute une rencontre à la liste des rencontres utilisant la règle
     *  
     * @param Game $game La rencontre à ajouter
     */
    public function addGame(Game $game) {
        $this->games->add($game);
    }
    
    /**
     * Supprime une rencontre de la liste des rencontres utilisant la règle
     * @param Game $game La rencontre à supprimer
     */
    public function removeGame(Game $game) {
        $this->games->removeElement($game);
    }

}
