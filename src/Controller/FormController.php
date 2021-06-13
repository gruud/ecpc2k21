<?php

namespace App\Controller;

use App\Component\PredictionChecker;
use App\Entity\Leaderboard;
use App\Entity\User;
use App\Form\GameType;
use App\Form\PredictionType;
use App\Component\LeaderBoardManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Prediction;
use App\Entity\Game;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * La classe FormController prend en charge les contrôleurs chargés d'afficher
 * et de valider les différents formulaires du site. 
 *
 * @author Sébastien ZINS
 */
class FormController extends AbstractController {

    /**
     * Contrôleur chargé d'assurer la saisie et l'enregistrement des pronostics
     * d'un joueur. Le formulaire est unique pour la création initial du pronostic
     * et sa modification.
     *
     * @param Request $request L'objet requête construit par Symfony
     * @param Security $security
     * @param EntityManagerInterface $manager
     * @param PredictionChecker $predictionChecker
     * @param SessionInterface|Session $session
     * @param string $gameId L'identifiant de la rencontre pour laquelle l'utilisateur
     * saisit un pronostic
     *
     * @return Response L'objet Response à renvoyer au client
     *
     * @Route("/game/{gameId}/prediction", name="ecpc_form_prediction")
     */
    public function predictionForm (
        Request $request,
        Security $security,
        EntityManagerInterface $manager,
        PredictionChecker $predictionChecker,
        SessionInterface $session,
        $gameId
    ): Response {

        //1. Récupération de l'utilisateur et vérification de ses droits
        // Note : on ne vérifie pas spécifique les rôles de l'utilisateur. 
        // Le firewall utilisé avec FosUserBundle nous garantit que seul des 
        // utilisateurs connectés pourront accéder à cette page. Comme le rôle
        // de base ROLE_USER suffit, aucune vérification complémentaire n'est
        // nécessaire.
        /** @var User|UserInterface $user */
        $user = $security->getUser();
        
        //2. Récupération de la rencontre. Si aucune rencontre n'est trouvé pour
        // l'identifiant, on renvoie une erreur 404.
        $game = $manager->getRepository(Game::class)->find($gameId);
        
        if ($game === null) {
            throw $this->createNotFoundException();
        }
        
        if (! $predictionChecker->canPredict($game)) {
            throw $this->createAccessDeniedException("Vous ne pouvez plus pronostiquer cette rencontre");
        }
        
        //3. Récupération du pronostic, s'il existe.
        $prediction = $manager->getRepository(Prediction::class)
                ->findUserPredictionForGame($user, $game);
        
        if ($prediction === null) {
            $prediction = new Prediction();
            $prediction->setGame($game);
            $prediction->setUser($user);
        }


        $form = $this->createForm(PredictionType::class, $prediction, compact('user', 'game', 'predictionChecker'));
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid() && $form->getClickedButton()->getName() === 'submit') {

                $manager->persist($prediction);
                $manager->flush();
                $session->getFlashBag()->add('success', $this->getPredictionSuccessMessage($prediction));
                return $this->redirectToSource($request);
            }

            if ($form->getClickedButton()->getName() === 'cancel') {
                return $this->redirectToSource($request);
            }
        }
        
        //4. Mise en place du formulaire
        return $this->render('form/form_prediction.html.twig', [
            'form' => $form->createView(),
            'game' => $game,
        ]);
    }
    
    /**
     * Méthode de contrôleur en charge du formulaire de saisie d'un score de 
     * rencontre. 
     * 
     * @param Request $request La requête Symfony
     *
     * @Route("/game/{gameId}/result", name="ecpc_form_game")
     */
    public function gameForm(
        Request $request,
        EntityManagerInterface $manager,
        LeaderBoardManager $leaderBoardManager,
        $gameId
    ) {
        
        //Seul un utilisateur disposant du rôle d'administrateur peut saisir le 
        //résultat. 
        if (! $this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne disposez pas des '
                    . 'droits suffisants pour saisir le résultat de la rencontre');
        }
        
        
        //Récupération de la rencontre
        $game = $manager->getRepository(Game::class)->find($gameId);
        
        if (null === $game) {
            throw $this->createNotFoundException();
        }
        
        //Initialisation du formulaire
        $form = $this->createForm(GameType::class, $game, ['game' => $game]);
        $form->handleRequest($request);
        
        //Traitement du formulaire
        if ($form->isSubmitted()) {
            if ($form->getClickedButton()->getName() == "submit" && $form->isValid()) {
                $leaderBoardManager->computeLeaderboard();
                $leaderBoardManager->computeCrewLeaderboard();
                $manager->flush();
                $this->get('session')->getFlashBag()->add('success', $this->getGameSuccessMessage($game));
            }

            return $this->redirectToSource($request);
        }
        
        return $this->render('form/form_game.html.twig', [
            'form' => $form->createView(),
            'game' => $game
        ]);
        
    }
    
    /**
     * Renvoie un objet Response permettant de rediriger l'utilisateur vers la 
     * source indiquée dans le paramètre 'source' de la requête. Si ce paramètre
     * n'existe pas, l'utilisateur est redirigé vers la page d'accueil.
     * 
     * @param Request $request La requête 
     * @return Response La réponse de redirection
     */
    private function redirectToSource($request) {
        $redirection  =$request->get('source', $this->generateUrl('ecpc_home'));
        return $this->redirect($redirection);
    }
    
    /**
     * Crée un message indiquant que le score de la rencontre a bien été
     * enregistré
     * @param Game $game La rencontre
     * @return string La chaîne générée, prête à être employée dans un message
     * flash
     */
    private function getGameSuccessMessage(Game $game) {
        return "Le score de la rencontre"
        . $game->getHomeTeam()->getName() . " - "
        . $game->getAwayTeam()->getName() . " : "
        . $game->getGoalsHome() . ' - ' . $game->getGoalsAway()
        . " a bien été enregistré";
    }
    
    /**
     * Crée un message indiquant que le pronostic a été pris en compte
     * 
     * @param Prediction $prediction Le pronostic réalisé
     * @return string Une chaîne de caractères. 
     */
    private function getPredictionSuccessMessage(Prediction $prediction) {
        return 'Votre pronostic a bien été enregistré :'
        . $prediction->getGame()->getHomeTeam()->getName() . " - "
        . $prediction->getGame()->getAwayTeam()->getName() . " : "
        . $prediction->getGoalsHome() . ' - ' . $prediction->getGoalsAway();
    }
}
