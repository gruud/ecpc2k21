<?php

namespace App\Controller;

use App\Component\PredictionChecker;
use App\Entity\Game;
use App\Entity\Leaderboard;
use App\Entity\Prediction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * La classe HomeController implémente le contrôleur de la page d'accueil de 
 * l'application, accessible une fois l'utilisateur connecté. 
 *
 * @author Sébastien ZINS
 */
class HomeController extends AbstractController {
    
    /**
     * Méthode d'entrée du contrôleur pour la route "wcpc2k18_home"
     *
     * @Route("/", name="ecpc_home")
     * @return Response La réponse à renvoyer au client
     */
    public function index(Security $security, EntityManagerInterface $manager, PredictionChecker $predictionChecker) {
        
        //1. Récupération des prochaines rencontres à pronostiquer, agrémentées des pronostics
        // de l'utilisateur connecté.
        /** @var User $user */
        $user = $security->getUser();
        $nextGames = $manager->getRepository(Game::class)->findNextGames($user);
        $lastGames = $manager->getRepository(Game::class)->findLastGameResults(8);
        
        $userPredictions = $manager
                ->getRepository(Prediction::class)
                ->findUserPredictionsIndexedByGameId($user);
        
        //2. Récupération du classement général
        $leaderboard = $manager->getRepository(Leaderboard::class)
                ->getFullLeaderboardOrderedForGeneral();
        
        
        
    return $this->render('home/home.html.twig',
            compact('nextGames', 'lastGames', 'predictionChecker', 'userPredictions', 'leaderboard'));
    }
}
