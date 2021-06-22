<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * La classe CrewRepository implémente un dépôt Doctrine personnalisé pour les 
 * services engagés dans le concours
 *
 * @author Sébastien ZINS
 */
class CrewRepository extends EntityRepository {
    
    
    /**
     * Récupère la liste des services avec les données utilisateurs associées
     */
    public function findWithUsers() {
        $qb = $this->createQueryBuilder('c');
        $qb->join('c.users', 'u')->addSelect('u');
        $qb->orderBy('c.name', 'ASC');
        $qb->addOrderBy('u.lastName', 'ASC')->addOrderBy('u.firstName', 'ASC');
        
        return $qb->getQuery()->getResult();
    }
}
