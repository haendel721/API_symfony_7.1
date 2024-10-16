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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/api/liste/user/json', name: 'app_liste_user_json_afficher', methods: ['GET'])]
public function userlistejsonindex(UserRepository $userRepository): JsonResponse
{
    $user = $userRepository->findAll();
    $userdata = [];
    foreach ($user as $users) {
        $userdata[] = [
            'id' => $users->getId(),
            'e-mail' => $users->getEmail(),
            'rôle' => $users->getRoles(),
            'password' => $users->getPassword(),
            'nom' => $users->getName(),
            'prénom' => $users->getSurname(),
            'date de naissance' => $users->getDateNaissance(),
            'address' => $users->getLot(),
            'image' => $users->getImage(),
            'situation familiale' => $users->getSituationFamiliale(),
            'lieu de naissance' => $users->getLieuNaissance(),
            'genre' => $users->getGenre(),
            'fonction' => $users->getFonction(),
        ];
    }

    return new JsonResponse($userdata);
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
