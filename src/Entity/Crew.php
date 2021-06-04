<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * La classe Crew implémente une équipe de pronostics
 *
 * @author sebastienzins
 * 
 * @ORM\Entity(repositoryClass="App\Repository\CrewRepository")
 * @ORM\Table(name="ecpc_crews")
 */
class Crew {
    
    /**
     *
     * @var integer L'identifiant unique de l'équipe
     * 
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     *
     * @var string Le nom de l'équipe;
     * 
     * @ORM\Column(type="string", length=128)
     */
    private $name;
    

    /**
     *
     * @var float Les points de l'équipe pour le classement général
     * @ORM\Column(type="float", nullable=true)
     */
    private $points;
    
    /**
     *
     * @var ArrayCollection | User[] La liste des utilisateurs de l'équipe
     * 
     * @ORM\OneToMany(targetEntity="User", mappedBy="crew")
     */
    private $users;
    
    
    
    public function __construct() {
        $this->users = new ArrayCollection();
    }
    /**
     * Récupère l'identifiant de l'équipe
     * @return integer L'identifiant de l'équipe
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Récupère le nom de l'équipe
     * @return string nom de l'équipe
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Positionne le nom de l'équipe
     * @param string $name Le nom à positionner
     */
    public function setName(string $name) {
        $this->name = $name;
    }
    
    /**
     * Récupère les points de l'équipe au classement par équipe
     * @return float Les points de l'équipe
     */
    public function getPoints(): float {
        return $this->points;
    }

    /**
     * 
     * @param double $points Positionne les points de l'équipe pour le classement
     * par équipe
     */
    public function setPoints(float $points) {
        $this->points = $points;
    }
    
    /**
     * Récupère la liste des utilisateurs appartenant à l'équipe
     * @return ArrayCollection | User[] La liste des utilisateurs de l'équipe
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Positionne la liste des utilisateurs de l'équipe
     * @param ArrayCollection $users Les utilisateurs à lier à l'équipe
     */
    public function setUsers(ArrayCollection $users) {
        $this->users = $users;
    }
    
    /**
     * Ajoute un utilisateur à l'équipe
     * @param User $user L'utilisateur à ajouter
     */
    public function addUser(User $user) {
        $this->users->add($user);
    }
    
    /**
     * Supprime un utiisateur de l'équipe
     * @param User $user L'utilisateur à supprimer
     */
    public function removeUser(User $user) {
        $this->users->removeElement($user);
    }

}
