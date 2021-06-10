<?php

namespace App\Controller;

use App\Entity\Leaderboard;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LeaderboardController extends AbstractController {

    /**
     * Permet d'afficher le classement général
     *
     * @Route("/leaderboards", name="ecpc_leaderboards")
     */
    public function show(EntityManagerInterface $manager) {

        /** @var Leaderboard[] $leaderboard */
        $leaderboard = $manager->getRepository(Leaderboard::class)
            ->getFullLeaderboardOrderedForGeneral();


        return $this->render('leaderboard/leaderboard.html.twig', ['leaderboard' => $leaderboard]);
    }

}