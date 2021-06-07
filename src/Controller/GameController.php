<?php

namespace App\Controller;

use App\Component\ChartManager;
use App\Component\PredictionChecker;
use App\Entity\Game;
use App\Entity\Prediction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * La classe GameController implémente le contrôleur prenant en charge la page
 * des rencontres de la compétition. Cette page présente la liste complète des
 * matches, avec la possibilité de consulter le détail pour chacun d'entre eux.
 *
 * @author Sébastien ZINS
 */
class GameController extends AbstractController {

    //put your code here

    /**
     * @param EntityManagerInterface $manager
     * @param PredictionChecker $predictionChecker
     * @param Security $security
     * @return Response
     *
     * @Route("/games", name="ecpc_games")
     */
    public function showAll(EntityManagerInterface $manager, PredictionChecker $predictionChecker, Security $security) {
        $games = $manager->getRepository(Game::class)->findAllWithTeams();
        /** @var User $user */
        $user = $security->getUser();
        $userPredictions = $manager
                ->getRepository(Prediction::class)
                ->findUserPredictionsIndexedByGameId($user);

        return $this->render('game/games_list.html.twig', [
                    "games" => $games,
                    "predictionChecker" => $predictionChecker,
                    "userPredictions" => $userPredictions,
        ]);
    }

    /**
     * Méthode de contrôleur prenant en charge l'affichage du résultat de la rencontre
     * 
     * @param integer $gameId L'identifiant de la rencontre
     *
     * @Route("/game/{gameId}", name="ecpc_game")
     */
    public function show(
        EntityManagerInterface $manager,
        ChartManager $chartManager,
        int $gameId,
        $trendsDatapoolMinSize
    ) {
        //Récupération de la rencontre
        $game = $manager->getRepository(Game::class)->find($gameId);
        if (null === $game) {
            throw $this->createNotFoundException();
        }

        //Récupération des prédictions pour la rencontre
        $predictions = $manager->getRepository(Prediction::class)
                ->findPredictionsForGame($game);

        $trendsData = $chartManager::getPredictionTrendsChartData($game, $predictions, $trendsDatapoolMinSize);

        return $this->render("game/game.html.twig", [
            "game" => $game,
            "predictions" => $predictions,
            "predictionPoolMinSize" => $trendsDatapoolMinSize,
            "trendsData" => $trendsData,
        ]);
    }

     
}
