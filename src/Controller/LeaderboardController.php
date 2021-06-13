<?php

namespace App\Controller;

use App\Entity\CrewLeaderboard;
use App\Entity\Leaderboard;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LeaderboardController extends AbstractController {

    /**
     * Permet d'afficher le classement général
     *
     * @Route("/leaderboards/{pane}", name="ecpc_leaderboards", defaults={"pane": "general"})
     */
    public function show(EntityManagerInterface $manager, $pane) {

        /** @var Leaderboard[] $leaderboard */
        $leaderboard = $manager->getRepository(Leaderboard::class)
            ->getFullLeaderboardOrderedForGeneral();

        $crewLeaderboard = $manager->getRepository(CrewLeaderboard::class)
            ->findAllForCrewLeaderboardDisplay();


        return $this->render('leaderboard/leaderboard.html.twig', [
            'leaderboard' => $leaderboard,
            'crewLeaderboard' => $crewLeaderboard,
            'pane' => $pane
        ]);
    }

}