<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserProfilSimpleController extends AbstractController
{
    #[Route('/user/profil/simple', name: 'app_user_profil_simple')]
    public function index(): Response
    {
        return $this->render('user_profil_simple/index.html.twig', [
            'controller_name' => 'UserProfilSimpleController',
        ]);
    }
}
