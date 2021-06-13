<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CrewLeaderboardRepository extends EntityRepository {


    /**
     * @return int|mixed|string
     */
    public function findAllForCrewLeaderboardDisplay() {
        $qb = $this->createQueryBuilder('cl');
        $qb->leftJoin('cl.crew', 'c');
        $qb->orderBy('cl.points', 'DESC')
            ->addOrderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}