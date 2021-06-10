<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * La classe LeaderboardRepository étend le dépôt standard Doctrine pour le
 * requêtage du classement.
 *
 * @author Sébastien Zins
 */
class LeaderboardRepository extends EntityRepository {
    
    
    /**
     * Renvoie le classement général, agrémenté des informations des utilisateurs,
     * et classé par rang de classement croissant, pour afficher le classement 
     * général de la compétition.
     */
    public function getFullLeaderboardOrderedForGeneral(){
        
        $qb = $this->createQueryBuilder('l');
        $qb->join('l.user', 'u')->addSelect('u');
        $qb->orderBy('l.points', "DESC")
                ->addOrderBy('u.lastName', "ASC")
                ->addOrderBy('u.firstName', "ASC");
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * 
     * @return array Le classement général sous la forme d'entrées indexées
     * par l'identifiant utilisateur. Utilisé pour les recalculs complets 
     * de classements.
     */
    public function findIndexedByUserIdArray() {
        $qb = $this->createQueryBuilder('l');
        $qb->join('l.user', 'u')->addSelect('u');
        
        $leaderboard = $qb->getQuery()->getResult();
        $lbArray = [];
        
        foreach($leaderboard as $boardItem) {
            $lbArray[$boardItem->getUser()->getId()] = $boardItem;
        }
        
        return $lbArray;
    }
}
