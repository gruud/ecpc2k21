<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * La classe CrewLeaderboard implémente le classement par équipe
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\CrewLeaderboardRepository")
 * @ORM\Table(name="ecpc_crew_leaderboard")
 */
class CrewLeaderboard {

    /**
     * @var int L'identifiant de l'élément de classement
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var Crew L'équipe visée par l'élément de classement
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Crew", mappedBy="leaderboard")
     * @ORM\JoinColumn(name="crew_id", referencedColumnName="id")
     */
    private $crew;

    /**
     * @var float Le nombre de points engrangés par l'équipe
     *
     * @ORM\Column(type="float", name="points");
     */
    private $points;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * @return Crew
     */
    public function getCrew(): Crew {
        return $this->crew;
    }

    /**
     * @param Crew $crew
     */
    public function setCrew(Crew $crew): void {
        $this->crew = $crew;
    }

    /**
     * @return float
     */
    public function getPoints(): float {
        return $this->points;
    }

    /**
     * @param float $points
     */
    public function setPoints(float $points): void {
        $this->points = $points;
    }
}