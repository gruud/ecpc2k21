<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * La classe UserController implémente les contrôleurs ayant lien avec les utilisateurs
 * de l'application (liste des utilisateurs, profil, etc.)
 *
 * @author Sébastien ZINS
 */
class UserController extends AbstractController {
    
    /**
     * Contrôleur permettant l'affichage des participants
     *
     * @Route("/users", name="ecpc_users")
     *
     */
    public function list() {
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findBy([], [
            "lastName" => "ASC",
            "firstName" => "ASC",
        ]);
        
        return $this->render('user/users_list.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @param $userId
     * @return Response
     *
     * @Route("/user/{userId}", name="ecpc_user")
     */
    public function detail($userId) {
        $user = $this->getDoctrine()->getManager()
                ->getRepository(User::class)
                ->findWithPredictionsOrderedByGameId($userId);
        
        if (null === $user) {
            throw $this->createNotFoundException();
        }
        
        return $this->render('user/user_detail.html.twig', [
            'user' => $user,
        ]);
    }
}
