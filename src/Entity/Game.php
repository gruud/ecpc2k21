<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * La classe Game implémente une rencontre de la coupe du monde, sur laquelle les
 * joueurs vont pouvoir pronostiquer. 
 *
 * @author Sébastien ZINS
 * 
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 * @ORM\Table(name="ecpc_games")
 */
class Game {
    
    
    /**
     * Type de rencontre : régulière; se termine au bout du temps réglementaire,
     * et peut finir sur un match nul
     */
    const TYPE_REGULAR = 0;
    
    /**
     * Type de rencontre:  éliminatoire; il y a forcément un vainqueur, avec 
     * prolongations et tirs aux buts si nécessaire. 
     */
    const TYPE_PLAYOFF = 1;
    
    /**
     * Résultat de la rencontre : indéfini - Le match n'a pas été joué
     */
    const RESULT_WINNER_UNDEF   = -1;
    /**
     * Résultat de la rencontre : l'équipe à domicile gagne
     */
    const RESULT_WINNER_HOME    = 0;
  
    /**
     * Résultat de la rencontre : l'équipe à l'extérieur gagne
     */
    const RESULT_WINNER_AWAY    = 1;
    
    /**
     * Résultat de la rencontre : Match nul
     */
    const RESULT_DRAW           = 2;
    
    /**
     *
     * @var integer L'identifiant unique de la rencontre
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;
    
    /**
     *
     * @var DateTime La date du coup d'envoi de la rencontre, heure universelle
     * 
     * @ORM\Column(name="kickoff", type="datetime")
     */
    private $kickoff;
    
    /**
     *
     * @var integer Le type de la rencontre. UNe rencontre peut être de deux types : 
     *  - Game::TYPE_REGULAR : Rencontre classique se terminant au bout de 90 minutes
     *  - Game::TYPE_PLAYOFF : Rencontre élminiatoire pouvant aller jusqu'aux tirs aux buts
     */
    private $type = self::TYPE_REGULAR;
    
    /**
     *
     * @var Team L'équipe jouant à domicile (première équipe décrite dans le 
     * détail de la rencontre)
     * 
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="gamesHome");
     * @ORM\JoinColumn(name="team_home_id", referencedColumnName="id")
     */
    private $homeTeam;
    
    /**
     *
     * @var Team L'équipe jouant à l'extérieur (seconde équipe décrite dans le 
     * détail de la rencontre).
     * 
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="gamesAway")
     * @ORM\JoinColumn(name="team_away_id", referencedColumnName="id")
     */
    private $awayTeam;
    
    /**
     *
     * @var integer Le nombre de buts marqués par l'équipe jouant à domicile.
     * 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $goalsHome;
    
    /**
     *
     * @var integer Le nombre de buts marqués par l'équipe jouant à l'extérieur 
     * 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $goalsAway;
    
    /**
     *
     * @var boolean Indique sur des prolongations ont été jouées pour ce match 
     * 
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $extraTime = false;
    
    /**
     *
     * @var integer Le nombre de pénalties marqués par l'équipe à domicile
     * 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $penaltiesHome;
    
    /**
     *
     * @var integer Le nombre de penalties marqués par l'équipe à l'extérieur
     * 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $penaltiesAway;
    
    /**
     *
     * @var string Le nom de la journée durant laquelle se joue le match
     * 
     * @ORM\Column(name="game_phase", type="string", length=128)
     */
    private $phase;
    
    
    /**
     * Les règles du jeu applicables à cette rencontre pour le calcul des
     * points utilisateurs
     * 
     * @var GameRule La règle du jeu utilisée pour cette rencontre
     * 
     * @ORM\ManyToOne(targetEntity="GameRule", inversedBy="games")
     * @ORM\JoinColumn(name="rule_id", referencedColumnName="id")
     */
    private $rule;
    
    /**
     *
     * @var string Le groupe d'appartenance de la rencontre dans lequel se 
     * joue le match. Utilisé uniquement durant la phase de poules
     * 
     * @ORM\Column(name="game_group", type="string", length=1)
     */
    private $group;
    
    /**
     *
     * @var ArrayCollection | Prediction[] Les pronostics réalisés pour cette
     * rencontre
     * 
     * @ORM\OneToMany(targetEntity="Prediction", mappedBy="game")
     */
    private $predictions;
    
    
    
    public function __construct() {
        $this->predictions = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant unique de la rencontre
     *
     * @return int L'identifiant unique de la rencontre
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Récupère la date du coup d'envoi de la rencontre
     * 
     * @return DateTime La date du coup d'envoi de la rencontre
     */
    public function getKickoff(): DateTime {
        return $this->kickoff;
    }

    /**
     * Récupère le type de la rencontre (rencontre normale ou éliminatoire)
     * 
     * @return integer le type de la rencontre
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * Récupère l'équipe jouant la rencontre à domicile
     * 
     * @return Team L'équipe à domicile
     */
    public function getHomeTeam(): Team {
        return $this->homeTeam;
    }

    /**
     * Récupère l'équipe jouant la rencontre à l'extérieur
     * 
     * @return Team L'équipe à l'extérieur
     */
    public function getAwayTeam(): Team {
        return $this->awayTeam;
    }

    /**
     * Récupère le nombre de buts marqués par l'équipe jouant à domicile
     * 
     * @return int|null Le nombre de buts marqués par l'équipe à domicile
     */
    public function getGoalsHome(): ?int {
        return $this->goalsHome;
    }

    /**
     * Récupère le nombre de buts marqués par l'équipe jouant à l'extérieur
     * 
     * @return int|null Le nmbre de buts marqués par l'équipe à l'extérieur
     */
    public function getGoalsAway(): ?int {
        return $this->goalsAway;
    }

    /**
     * Indique si des prolongations ont eu lieu durant la rencontre
     * 
     * @return boolean VRAI si des prolongations ont été jouées, FAUX sinon
     */
    public function getExtraTime(): bool {
        return $this->extraTime;
    }

    /**
     * Récupère le nombre de penalties marqués par l'équipe à domicile durant
     * la séance de tirs aux buts, si cette dernière a eu lieu
     * 
     * @return int|null Le nombre de penalties de l'équipe à domicile
     */
    public function getPenaltiesHome(): ?int {
        return $this->penaltiesHome;
    }

    /**
     * Récupère le nombre de penalties marqués par l'équipe à l'extérieur durant
     * la séance de tirs aux bvuts, si cette dernière a eu lieu
     * 
     * @return int|null Le nombre de penalties de l'équipe à l'extérieur
     */
    public function getPenaltiesAway(): int {
        return $this->penaltiesAway;
    }
    
    /**
     * Récupère la règle du jeu à utiliser pour calculer les points de cette
     * rencontre
     * @return GameRule|null La règle du jeu utilisée pour cette rencontre
     */
    public function getRule(): ?GameRule {
        return $this->rule;
    }

    /**
     * Positionne la date du coup d'envoi
     * 
     * @param DateTime $kickoff La date à positionner
     */
    public function setKickoff(DateTime $kickoff) {
        $this->kickoff = $kickoff;
    }

    /**
     * Positionne le type de la rencontre
     * @param integer $type Le type de la rencontre. Peut être l'une des valeurs
     * suivantes : self::TYPE_REGULAR ou self::TYPE_PLAYOFF
     * 
     * @throws Exception si le type donné en paramètre n'est pas valide.
     */
    public function setType(int $type) {
        $acceptedTypes = [self::TYPE_REGULAR, self::TYPE_PLAYOFF];
        
        if (!in_array($type, $acceptedTypes)) {
            throw new Exception("Type de rencontre invalide : " . $type .  "."
                    . " Le type doit être l'une des valeurs suivantes "
                    . "[" . implode(", ", $acceptedTypes) . "]");
        }
        $this->type = $type;
    }

    /**
     * Positionne l'équipe à domicile
     *
     * @param Team $homeTeam L'équipe à positionner
     * @throws Exception
     */
    public function setHomeTeam(Team $homeTeam) {
        $this->checkTeams($homeTeam, $this->awayTeam);
        $this->homeTeam = $homeTeam;
    }

    /**
     * Positionne l'équipe à l'extérieur
     *
     * @param Team $awayTeam L'équipe à positionner
     * @throws Exception
     */
    public function setAwayTeam(Team $awayTeam) {
        $this->checkTeams($awayTeam, $this->homeTeam);
        $this->awayTeam = $awayTeam;
    }

    /**
     * Positionne le nombre de buts marqués par l'équipe à domicile
     *
     * @param integer $goalsHome Le score à positionner
     * @throws Exception
     */
    public function setGoalsHome(int $goalsHome) {
        $this->checkValidScore($goalsHome);
        $this->goalsHome = $goalsHome;
    }

    /**
     * Positionne le nombre de buts marqués par l'équipe à l'extérieur
     *
     * @param integer $goalsAway Le score à positionner
     * @throws Exception
     */
    public function setGoalsAway($goalsAway) {
        $this->checkValidScore($goalsAway);
        $this->goalsAway = $goalsAway;
    }

    /**
     * Positionne l'indicateur montrant si des prolongations ont eu lieu
     * durant la rencontre
     * 
     * @param boolean $extraTime L'indicateur de prolongations à positionner
     */
    public function setExtraTime(bool $extraTime) {
        $this->extraTime = $extraTime;
    }

    /**
     * Positionne le nombre de penalties marqués par l'équipe à domicile durant
     * la séance de tirs aux buts
     * 
     * @param integer $penaltiesHome Le nombre de penalties à positionner
     */
    public function setPenaltiesHome(int $penaltiesHome) {
        $this->checkValidScore($penaltiesHome);
        $this->penaltiesHome = $penaltiesHome;
    }

    /**
     * Positionne le nombre de penalties marqués par l'équipe à l'extérieur 
     * durant la séance de tirs aux buts
     * 
     * @param integer $penaltiesAway Le nombre de penalties à positionner
     */
    public function setPenaltiesAway(int $penaltiesAway) {
        $this->penaltiesAway = $penaltiesAway;
    }
    
    /**
     * Récupère la phase d'appartenance de la rencontre
     * 
     * @return string la phase d'appartenance de la rencontre
     */
    public function getPhase() {
        return $this->phase;
    }

    /**
     * Récupère la liste des pronostics réalisés pour la rencontre
     * 
     * @return ArrayCollection La liste des pronostics réalisés pour la 
     * rencontre
     */
    public function getPredictions() {
        return $this->predictions;
    }
    
    /**
     * Renvoie le vainqueur de la rencontre sous la forme d'un entier.
     * 
     * @return integer Renvoie le résultat de la rencontre
     */
    public function getResult() {
        if ($this->getGoalsHome() === null) {
            return self::RESULT_WINNER_UNDEF;
        } elseif($this->getGoalsHome() == $this->getGoalsAway()) {
            return self::RESULT_DRAW;
        } elseif($this->getGoalsHome() > $this->getGoalsAway()) {
            return self::RESULT_WINNER_HOME;
        } else {
            return self::RESULT_WINNER_AWAY;
        }
    }
    
    /**
     * Récupère la différence de but de la rencontre (i.e. la différence entre
     * le nombre de but marqués par l'équipe à domicile et le nombre de buts
     * marqués par l'équipe à l'extérieur). Utilisé notamment pour vérifier
     * si l'utilisateur a la bonne différence de buts ou non
     *
     * @return int La différence de but du match
     */
    public function getGA(): int {
        return $this->goalsHome - $this->goalsAway;
    }

    /**
     * Positionne la phase d'appartenance de la rencontre
     * 
     * @param string $phase La phase à positionner
     */
    public function setPhase(string $phase) {
        $this->phase = $phase;
    }

    /**
     * Positionne la liste des pronostics réalisés pour cette rencontre
     * 
     * @param ArrayCollection $predictions La liste des pronostics à positionner
     */
    public function setPredictions(ArrayCollection $predictions) {
        $this->predictions = $predictions;
    }
    
    /**
     * Ajoute le pronostic $prediction à la liste des pronostics réalisés pour 
     * la rencontre
     * 
     * @param Prediction $prediction Le pronostic à ajouter
     */
    public function addPrediction(Prediction $prediction) {
        $this->predictions->add($prediction);
    }
    
    /**
     * Supprime le pronostic $prediction de la liste des pronostics réalisés pour
     * la rencontre
     * 
     * @param Prediction $prediction Le pronostic à supprimer
     */
    public function removePrediction(Prediction $prediction) {
        $this->predictions->removeElement($prediction);
    }

        
    /**
     * Vérifie si la rencontre n'oppose pas une équipe avec elle-même. Cette
     * méthode est utilisée uniquement par le mutateur de homeTeam et awayTeam. 
     * 
     * @param Team $firstTeam La première équipe
     * @param Team $secondTeam La seconde équipe
     * @throws Exception si $firstTeam et $secondTeam sont la seule et même équipe
     */
    private function checkTeams(Team $firstTeam, Team $secondTeam = null) {
        if ($secondTeam != null && $firstTeam === $secondTeam) {
            throw new Exception("L'équipe " . $firstTeam->getName() . ""
                    . " ne peut pas jouer contre elle-même");
        }
    }
    
    /**
     * Vérifie si une composante d'un score est valide. Une telle composante est
     * valide si et seulement si il s'agit d'un entier supérieur ou égal à zéro. 
     * 
     * @param int $scoreItem La composante d'un score à vérifier
     * @throws Exception si la composante est invalide
     */
    private function checkValidScore(int $scoreItem) {
        if (is_integer($scoreItem) && $scoreItem < 0) {
            throw new Exception("Les composantes d'un score doivent être des "
                    . "entiers supérieurs à zéro.");
        }
    }
    
    /**
     * Récupère le nom du groupe dans lequel se joue la rencontre
     * 
     * @return string Le groupe dans lequel se joue la rencontre
     */
    public function getGroup(): string {
        return $this->group;
    }

    /**
     * Positionne le nom du groupe dans lequel se joue la rencontre
     * 
     * @param string $group Le nom du groupe à positionner
     */
    public function setGroup(string $group) {
        $this->group = $group;
    }
    
    /**
     * Positionne l'identifiant unique de la rencontre
     * 
     * @param integer $id L'identifiant unique à positionner
     */
    public function setId(int $id) {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function __toString() {
        return $this->id . " : " . $this->getHomeTeam()->getName() 
                . "-" . $this->getAwayTeam()->getName();
    }
    
    
    /**
     * Positionne la règle du jeu à utiliser pour compter les points de pronostics
     * pour cette rencontre
     * @param GameRule $rule La règle à positionner
     */
    public function setRule(GameRule $rule) {
        $this->rule = $rule;
    }
    
    /**
     * Vérifie si la rencontre a déjà commencée
     * 
     * @return boolean VRAI si la rencontre a commencé, FAUX sinon.
     */
    public function hasStarted(): bool {
        $now = new DateTime();
        return $this->kickoff < $now;
    }
}
