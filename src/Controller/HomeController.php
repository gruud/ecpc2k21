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
        /** @var Leaderboard[] $leaderboard */
        $leaderboard = $manager->getRepository(Leaderboard::class)
                ->getFullLeaderboardOrderedForGeneral();

        // Récupération de la position de l'utilisateur dans la liste pour déterminer les index de début et de fin
        // d'affichage

        $position = 0;
        for ($i = 0; $i < count($leaderboard); $i++) {

            $lbitem = $leaderboard[$i];
            if ($lbitem->getUser()->getId() == $user->getId()) {
                $position = $i;
            }
        }

        $lbStartPos = 0;
        $lbEndPos = 0;
        if ($position > 0) {
            $lbStartPos = ($position - 3 > 0 ? $position - 3 : 0);
            $lbEndPos = ($position + 3 < count($leaderboard) ? $position + 3 : count($leaderboard) - 1);
        }
        
        
        
    return $this->render('home/home.html.twig',
            compact(
                'nextGames',
                'lastGames',
                'predictionChecker',
                'userPredictions',
                'leaderboard',
                'lbStartPos',
                'lbEndPos'
            ));
    }
}
