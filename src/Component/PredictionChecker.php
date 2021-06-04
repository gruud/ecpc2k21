<?php

namespace App\Component;

use App\Entity\Prediction;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use App\Entity\User;
use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Security;

/**
 * La classe PredictionChecker implémente le service responsable de la vérification
 * de la faisabilité et de la validité des pronostics réalisés par un joueur. 
 *
 * @author Sébastien ZINS
 */
class PredictionChecker {
    
    /**
     *
     * @var DateInterval La durée en minute, relativement au début de la rencontre,
     * avant laquelle l'utilisateur ne peut pas pronostiquer
     */
    private $predictionStartDelay;
    
    /**
     *
     * @var DateInterval La temps en minutes séparant l'utilisateur du début de la 
     * rencontre, à partir duquel il ne peut plus pronostiquer le score. 
     */
    private $predictionEndDelay;
    
    /**
     *
     * @var User L'utilisateur connecté
     */
    private $user;
    
    /**
     *
     * @var EntityManagerInterface | EntityManager Le gestionnaire d'entité Doctrine
     */
    private $manager;


    /**
     * Constructeur
     *
     * @param integer $predictionStartDelay La durée en minutes, relativement au début
     * de la rencontre, avant laquelle l'utilisateur ne peut pas pronostiquer
     * @param integer $predictionEndDelay La durée en minutes, relativement
     * au début de la rencontre, après laquelle l'utilisateur ne peut plus
     * pronostiquer
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $manager,
        Security $security,
        int $predictionStartDelay,
        int $predictionEndDelay
    ) {
        $this->predictionStartDelay = new DateInterval('PT' . $predictionStartDelay . 'S');
        $this->predictionEndDelay = new DateInterval('PT' . $predictionEndDelay . 'S');
        $this->user = $security->getUser();
        $this->manager = $manager;
    }
    
    /**
     * Vérifie un utilisateur peut effectuer un pronostic pour la 
     * rencontre $game. Un utilisateur peut pronostiquer une rencontre si 
     * le match est ouvert aux pronostics, i.e. si la date limite d'ouverture 
     * est passé, etsi la date de clôture n'est pas encore advenue. 
     * Les dates d'ouvertures et de clôtures sont calculées relativement à la
     * date de la rencontre en utilisant des intervalles prédéfinis 
     * $predictionStartDelay (pour l'ouverture) et $predictionEndDelay (pour la 
     * clôture). 
     * 
     * @param Game $game La rencontre sur laquelle porte le pronostic
     * 
     * @return bool TRUE si l'utilisateur peut pronostiquer la rencontre, FALSE sinon.
     */
    public function canPredict(Game $game): bool {
         
        return  !$this->gameStillClosed($game) && !$this->gameDeadlinePassed($game);
    }
    
    /**
     * Vérifie si un utilisateur administrateur peut saisir le résultat de la
     * rencontre. La saisie du résultat est ouverte deux heures après le début 
     * théorique de la rencontre
     * 
     * @param Game $game La rencontre à saisir
     * @return boolean VRAI si l'administrateur peut saisir le résultat d'une rencontre,
     * FAUX sinon
     */
    public function canFillGameResult(Game $game): bool {
        $start = clone $game->getKickoff();
        $gameFillDelay = new DateInterval('PT7200S');
        $start->add($gameFillDelay);
        
        $now = new DateTime();
        return $now > $start;
    }
    
    /**
     * Vérifie si une rencontre est encore fermée aux pronostics car la date 
     * d'ouverture n'est pas encore advenue. 
     * 
     * @param Game $game La rencontre pour laquelle on souhaite faire la vérification.
     * @return boolean VRAI si la rencontre est encore fermée, FAUX sinon. 
     */
    public function gameStillClosed(Game $game): bool {
        $start = clone $game->getKickoff();
        $now = new DateTime();
        $start->sub($this->predictionStartDelay);
        
        return $now < $start;
    }
    
    /**
     * Vérifie si la date limite de saisie d'un pronostic est passée. 
     * 
     * @param Game $game La rencontre pour laquelle effectuer cette vérification
     * @return bool si la date limite de saisie est passée, FAUX sinon.
     */
    public function gameDeadlinePassed(Game $game): bool {
        $end = clone $game->getKickoff();
        $now = new DateTime();

        $end->sub($this->predictionEndDelay);
        
        return $now > $end;
    }
    
    /**
     * Vérifie si l'utilisateur a déjà utilisé son jackpot pour la journée $day.
     * 
     * @param string $day Le nom de la journée considérée
     * @return boolean VRAI si l'utilisateur a déjà utilisé son jackpot pour la
     * journée $day, faux sinon.
     */
    public function jackpotUsedForDay(string $day): bool {
        $predictionWithJackpot = $this->manager
                ->getRepository(Prediction::class)
                ->findUserJackpotForDay($this->user, $day);
        
        return $predictionWithJackpot !== null;
    }
}
