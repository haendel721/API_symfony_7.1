<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController 
{
    #[Route('/admin/profil', name: 'app_profil')] 
    public function index(Security $security , SiteRepository $siteRepository): Response
    {
        $user = $security->getUser(); // Récupérer l'utilisateur connecté
        return $this->render('user/index.html.twig', [
            'users' => $user,
            'sites' => $siteRepository->findAll()
        ]);
    }

    #[Route('/assigner-role/{userId}', name: 'assigner_role')]
    public function assignerRole(int $userId, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Récupérer l'entité à mettre à jour
        $user = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
    
        // Mettre à jour l'autorisation
        $user->setRoles(["ROLE_ADMIN"]);
    
        // Enregistrer les modifications
        $entityManager->flush();
        $this->addFlash('success','Assignation  du rôle pour ' . $user->getName() . '   avec succès');
    
        // Rediriger vers la page précédente
        return $this->redirectToRoute('app_user_index');
    }

    
    #[Route('/retirer-role/{userId}', name: 'retirer_role')]
    public function retirerRole(int $userId, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Récupérer l'entité à mettre à jour
        $user = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
    
        // Mettre à jour l'autorisation
        $user->setRoles(["ROLE_USER"]);
    
        // Enregistrer les modifications
        $entityManager->flush();
        $this->addFlash('danger','Retrait du rôle pour ' . $user->getName() . '   avec succès');
    
        // Rediriger vers la page précédente
        return $this->redirectToRoute('app_user_index');
    }
}
