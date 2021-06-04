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
     * Renvoie une représentation de l'objet sous la forme d'une chaîne de 
     * caractères
     * @return string La chaîne associée à l'objet 
     */
    public function __toString() {
        return "Classement pour l'utilisateur " . $this->getUser()->getFirstName()
                . " " . strtoupper($this->getUser()->getLastName());
    }
}
