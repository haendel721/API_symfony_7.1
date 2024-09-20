<?php

namespace App\Controller;

use App\Entity\LoginSite;
use App\Entity\Site;
use App\Entity\User;
use App\Repository\LoginSiteRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController 
{
    #[Route('/admin/profil', name: 'app_profil')] 
    public function index(Security $security , LoginSiteRepository $loginSiteRepository , SiteRepository $siteRepository): Response
    {
        $user = $security->getUser(); // Récupérer l'utilisateur connecté
        return $this->render('user/index.html.twig', [
            'users' => $user,
            'loginSites' => $loginSiteRepository->findAll(),
            'sites' => $siteRepository->findAll()
        ]);
    }

    #[Route("/site/login/password/{siteId}", name:"login_and_password", methods:["POST"])]
    public function login_and_password(Request $request, EntityManagerInterface $entityManager , PasswordHasherFactoryInterface $passwordHasherFactory): Response
    {
        // Récupérer les données du formulaire
        $nameSite = $request->request->get('nameSite');
        $login = $request->request->get('login');
        $password = $request->request->get('password');
        $userId = $request->request->get('userId');
        $siteId = $request->request->get('siteId');
         // Trouver l'utilisateur et le site par l'ID
        $user = $entityManager->getRepository(User::class)->find($userId);
        $site = $entityManager->getRepository(Site::class)->find($siteId);
        if ($password) {
            $plainPassword = $request->request->get('password'); // Récupérer le mot de passe du formulaire

            // Utiliser le factory pour obtenir le hasher
            $passwordHasher = $passwordHasherFactory->getPasswordHasher('my_custom_hasher');

            // Hacher le mot de passe
            $hashedPassword = $passwordHasher->hash($plainPassword);
        }

        // Créer une nouvelle entité Site
        $loginSite = new LoginSite();
        $loginSite->setNameSite($nameSite);
        $loginSite->setLogin($login);
        $loginSite->setMdp($hashedPassword);
        $loginSite->setUser($user);
        $loginSite->setSite($site);

        // Enregistrer les données dans la base de données
        $entityManager->persist($loginSite);
        $entityManager->flush();

        // Rediriger vers une page de confirmation ou afficher un message
        return  $this->redirectToRoute('app_profil');
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
