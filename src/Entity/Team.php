<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * La classe Team implémente une équipe prenant part à la compétition. 
 *
 * @author Sébastien ZINS
 * 
 * @ORM\Entity
 * @ORM\Table(name="ecpc_teams")
 */
class Team {

    /**
     *
     * @var integer L'identifiant unique de l'équipe
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;
    
    /**
     *
     * @var string Le nom de l'équipe
     * 
     * @ORM\Column(type="string", length=64)
     */
    private $name;
    
    /**
     *
     * @var string Le nom abrégé de l'équipe, sur trois caractères
     * 
     * @ORM\Column(type="string", length=3)
     */
    private $abbreviation;
    
    
    /**
     *
     * @var ArrayCollection | Game[] Les rencontres jouées à domicile par cette équipe
     * 
     * @ORM\OneToMany(targetEntity="Game", mappedBy="homeTeam")
     */
    private $gamesHome;
    
    /**
     *
     * @var ArrayCollection | Game[] Les rencontres jouées à l'extérieur par cette équipe
     * 
     * @ORM\OneToMany(targetEntity="Game", mappedBy="awayTeam")
     */
    private $gamesAway;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->gamesHome = new ArrayCollection();
        $this->gamesAway = new ArrayCollection();
    }
    
   
    /**
     * Récupère l'identifiant unique de l'équipe
     * 
     * @return integer L'identifiant unique de l'équipe
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Récupère le nom de l'équipe
     * 
     * @return string Le nom de l'équipe
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Récupère l'abbréviation de l'équipe, sur trois lettres
     * 
     * @return string L'abbréviation de l'équipe, sur trois lettres
     */
    public function getAbbreviation() {
        return $this->abbreviation;
    }

    /**
     * Positionne l'identifiant de l'équipe
     * 
     * @param integer $id L'identifiant à positionner
     */
    public function setId($id) {
        $this->id = $id;
    }

        
    /**
     * Positionne le nom de l'équipe
     * 
     * @param string $name Le nom à positionner
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Positionne l'abbréviation de l'équipe. L'abbréviation fait obligatoirement
     * trois caractères de long. 
     * 
     * @param string $abbreviation L'abbréviation à positionner
     */
    public function setAbbreviation($abbreviation) {
        if(strlen($abbreviation) != 3) {
            throw new \Exception("L'abbréviation doit obligatoirement faire"
                    . "trois caractères de long");
        }
        $this->abbreviation = $abbreviation;
    }

}
