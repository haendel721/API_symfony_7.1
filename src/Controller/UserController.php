<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Security $security): Response
    {
        $user = $security->getUser(); // Récupérer l'utilisateur connecté
        return $this->render('user/index.html.twig', [
            'users' => $user,
        ]);
    }
}
