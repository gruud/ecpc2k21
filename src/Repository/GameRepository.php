<?php

namespace App\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use App\Entity\User;

/**
 * La classe GameRepository implémente un dépôt avancé pour l'entité Game 
 * et contient des classes de récupération spécifiques des rencontres de 
 * la compétition
 *
 * @author Sébastien ZINS
 */
class GameRepository extends EntityRepository {
    
    
    /**
     * Récupère la liste de l'ensemble des rencontres avec chargement immédiat
     * des informations équipe.
     * 
     * @return ArrayCollection La liste des rencontres présentes en base
     */
    public function findAllWithTeams() {
        $qb = $this->createQueryBuilder('g');
        $qb->leftJoin('g.homeTeam', 'ht');
        $qb->leftJoin('g.awayTeam', 'at');
        $qb->addSelect('ht')->addSelect('at');
        $qb->orderBy('g.id', 'ASC');
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Récupère les rencontres avec l'ensemble des données liées nécessaires
     * pour le calcul du classement.Seules les rencontres ayant un résultat 
     * son récupérées
     * @return type
     */
    public function findForLeaderboardCalculation() {
        $qb = $this->createQueryBuilder('g');
        $qb->leftJoin('g.predictions', 'p');
        $qb->leftJoin('p.user', 'u');
        $qb->addSelect('p')->addSelect('u');
        $qb->orderBy('g.id', 'ASC');
        $qb->where($qb->expr()->isNotNull('g.goalsHome'));
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Récupère la liste des prochaines rencontres que l'utilisateur doit 
     * pronostiquer (i.e. les rencontres ne sont pas encore terminées). 
     * Une rencontre est déclarée non terminée tant que son résultat n'est pas
     * saisi par un administrateur. Le critère de sélection se fait donc sur la 
     * saisie administrateur et non sur la date de la rencontre.
     */
    public function findNextGames(User $user) {
        $qb = $this->createQueryBuilder('g');
        $qb->leftJoin('g.predictions', 'p')->addSelect('p');        
        $qb->where($qb->expr()->isNull('g.goalsHome'));
        $qb->orderBy('g.kickoff', 'ASC');
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Récupère les derniers résultats de rencontres, dans la limite du paramètre
     * $maxResults
     * 
     * @param integer $maxResults Le nombre maximal de rencontres à renvoyer
     * @return type
     */
    public function findLastGameResults($maxResults) {
        $qb = $this->createQueryBuilder('g');
        $qb->where($qb->expr()->isNotNull('g.goalsHome'));
        $qb->setMaxResults($maxResults);
        $qb->orderBy('g.kickoff', 'DESC');
        
        
        return $qb->getQuery()->getResult();
    }
}
