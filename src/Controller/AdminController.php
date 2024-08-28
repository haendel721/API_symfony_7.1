<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    // #[Route('/admin', name: 'app_admin')]
    // public function index(): Response
    // {
    //     return $this->render('admin/index.html.twig', [
    //         'controller_name' => 'AdminController',
    //     ]);
    // }
    #[Route('/user', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $siteRepository): Response
    {
        return $this->render('security/index.html.twig', [
            'users' => $siteRepository->findAll(),
        ]);
    }

}
