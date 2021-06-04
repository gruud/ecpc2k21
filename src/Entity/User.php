<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="ecpc_users")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     *
     * @var string Le service d'appartenance de l'utilisateur
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $department;

    /**
     *
     * @var ArrayCollection | Prediction [] Les prédictions réalisées par
     * l'utilisateur.
     *
     * @ORM\OneToMany(targetEntity="Prediction", mappedBy="user")
     */
    protected $predictions;

    /**
     *
     * @var Leaderboard Le tableau des classements de l'utilisateur
     *
     * @ORM\OneToOne(targetEntity="Leaderboard", mappedBy="user")
     */
    protected $leaderboard;

    /**
     *
     * @var Crew L'équipe d'appartenance de l'utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Crew", inversedBy="users")
     * @ORM\JoinColumn(name="crew_id", referencedColumnName="id")
     */
    protected $crew;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername() {
        // TODO: Implement getUsername() method.
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Récupère le nom du service d'appartenance de l'utilisateur
     *
     * @return string Le service d'appartenance de l'utilisateur
     */
    public function getDepartment(): string {
        return $this->department;
    }

    /**
     * Positionne le service de l'utilisateur
     *
     * @param string $department Le service à positionner
     */
    public function setDepartment(string $department) {
        $this->department = $department;
    }

    /**
     * Récupère la liste des pronostics réalisés par l'utilisateur
     *
     * @return ArrayCollection|null La liste des pronostics réalisés par l'utilisateur
     */
    public function getPredictions(): ?ArrayCollection {
        return $this->predictions;
    }

    /**
     * Positionne la liste des pronostics réalisés par l'utilisateur
     *
     * @param ArrayCollection $predictions La liste des pronostics à positionner
     */
    public function setPredictions(ArrayCollection $predictions) {
        $this->predictions = $predictions;
    }

    /**
     * Ajoute un nouveau pronostic à la liste des pronostics réalisés par l'utilisateur
     *
     * @param Prediction $prediction Le pronostic à rajouter
     */
    public function addPrediction(Prediction $prediction) {
        $this->predictions->add($prediction);
    }

    /**
     * Récupère l'équipe d'appartenance de l'utilisateur
     * @return Crew L'équipe de l'utilisateur
     */
    public function getCrew() {
        return $this->crew;
    }

    /**
     * Positionne l'équipe d'appartenance de l'utilisateur
     * @param Crew $crew L'équipe à positionner
     */
    public function setCrew(Crew $crew = null) {
        $this->crew = $crew;
    }

    /**
     * Supprime le pronostic $prediction de la liste des pronostics réalisés par
     * l'utilisateur
     *
     * @param Prediction $prediction Le pronostic à supprimer
     */
    public function removePrediction(Prediction $prediction) {
        $this->predictions->removeElement($prediction);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString() {
        return $this->firstName . " " . strtoupper($this->lastName);
    }
}
