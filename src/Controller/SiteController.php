<?php

namespace App\Controller;

use App\Entity\LoginSite;
use App\Entity\Permission;
use App\Entity\Site;
use App\Entity\User;
use App\Form\SiteType;
use App\Repository\LoginSiteRepository;
use App\Repository\PermissionRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[Route('/site')]
class SiteController extends AbstractController
{
    #[Route('/', name: 'app_site_index', methods: ['GET'])]
    public function index(SiteRepository $siteRepository , PermissionRepository $permissionRepository , LoginSiteRepository $loginsiterepository): Response
    {
       
        return $this->render('site/index.html.twig', [
            'sites' => $siteRepository->findAll(),
            'permissions' => $permissionRepository->findAll(),
            'loginSites' => $loginsiterepository->findAll()
        ]);
    }

    
    #[Route('/api/liste/site/json', name: 'app_liste_site_json_afficher', methods: ['GET'])]
    public function sitelistejsonindex(SiteRepository $siteRepository): JsonResponse
    {
        $site = $siteRepository->findAll();
        $sitedata = [];
        foreach ($site as $sites) {
            $sitedata[] = [
                'id' => $sites->getId(),
                'site' => $sites->getName(),
                'url' => $sites->getUrl(),
                'utilisateur' => $sites->getUser()->getName(),
                'catégorie' => $sites->getCategorySite()->getName(),
                'id-login' => $sites->getIdLogin(),
                'class-login' => $sites->getClassLogin(),
                'id-mdp' => $sites->getIdMdp(),
                'class-mdp' => $sites->getClassMdp(),
                'class-submit' => $sites->getClassSubmit(),
            ];
        }

        return new JsonResponse($sitedata);
    }

    #[Route('/api/liste/site/Yes/json', name: 'app_liste_site_Yes_json_afficher', methods: ['GET'])]
public function psiteautoriselistejsonindex(SiteRepository $siteRepository, Security $security): JsonResponse
{
    $user = $security->getUser(); // Obtenir l'utilisateur connecté
    if (!$user) {
        return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
    }

    // Filtrer les sites autorisés pour l'utilisateur
    $sitesAutorises = $siteRepository->findByUserPermissions($user); // Méthode à définir dans votre repository
    // dd($sitesAutorises);
    $sitedata = [];
    foreach ($sitesAutorises as $sites) {
        $sitedata[] = [
            'id' => $sites->getId(),
            'site' => $sites->getName(),
            'url' => $sites->getUrl(),
            'login' => $sites->getLogin()->getLogin(),
            'password' => $sites->getLogin()->getMdp(),
            'utilisateur' => $sites->getUser()->getName(),
            'catégorie' => $sites->getCategorySite()->getName(),
            'id-login' => $sites->getIdLogin(),
            'class-login' => $sites->getClassLogin(),
            'id-mdp' => $sites->getIdMdp(),
            'class-mdp' => $sites->getClassMdp(),
            'class-submit' => $sites->getClassSubmit(),
        ];
    }

    return new JsonResponse($sitedata);
}


    #[Route('/liste', name: 'app_liste_site_index', methods: ['GET'])]
    public function ListeSIteIndex(SiteRepository $siteRepository ): Response
    {
       
        return $this->render('site/listeSite.html.twig', [
            'sites' => $siteRepository->findAll(),
        ]);
    }

   #[Route("/user/{id}/sites", name:"app_site_perso")]
    public function showUserSites(User $user): Response
    {
        // On récupère les permissions autorisées pour cet utilisateur
        $authorizedSites = [];
        foreach ($user->getPermissions() as $permission) {
            if ($permission->isAuthorized()) {
                $authorizedSites[] = $permission->getSite();
            }
        }

        return $this->render('site/index.html.twig', [
            'user' => $user,
            'sites' => $authorizedSites,
        ]);
    }

    #[Route('/new', name: 'app_site_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ): Response

    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();

            // // Récupérer le mot de passe en clair depuis la requête
            // $plainPassword = $request->request->get('password');

            // // Hacher le mot de passe (sans objet User)
            // $hashedPassword = $passwordHasher->hash($plainPassword);

            // Assigner automatiquement le site créer à des utilisateur 
            $user = $entityManager->getRepository(User::class)->findAll();
            // Ajouter automatiquement une permission "non autorisée" pour chaque site
            foreach ($user as $users) {
                $permission = new Permission();
                $permission->setUser($users);
                $permission->setSite($site);
                $permission->setAuthorized(false); // "Non autorisé"

                // Persister la permission pour chaque site
                $entityManager->persist($permission);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Ajout  de  ' . $site->getName() . ' pour '. $site->getName() . '  avec succes');

            return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site/new.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_site_show', methods: ['GET'])]
    public function show(Site $site): Response
    {
        return $this->render('site/show.html.twig', [
            'site' => $site,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_site_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Site $site, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Modification  de  ' . $site->getName() . ' pour '. $site->getName() . '  avec succes');

            return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site/edit.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_site_delete', methods: ['POST'])]
    public function delete(Request $request, Site $site, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$site->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($site);
            $entityManager->flush();
            $this->addFlash('danger', 'Suppression  de  ' . $site->getName() . ' pour '. $site->getName() . '  avec succes');
        }

        return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/delete', name: 'app_site_delete_direct')]
    public function supprimer( Site $site, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($site);
            $entityManager->flush();
            $this->addFlash('danger', 'Suppression  de  ' . $site->getName() . ' pour '. $site->getName() . '  avec succes');
        return $this->redirectToRoute('app_site_index');
    }
    private $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository , EntityManagerInterface $entityManager)
    {
        $this->permissionRepository = $permissionRepository;
        $this->entityManager = $entityManager;
    }
  
    #[Route('/site/{id}', name: 'site_show')]
    public function afficher(Site $site): Response
    {
        $user = $this->getUser(); // Supposons que l'utilisateur est connecté

        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated.');
        }

        // Vérifier les permissions directement dans le contrôleur
        $permission = $this->permissionRepository->findOneBy([
            'user' => $user,
            'site' => $site,
        ]);

        if (!$permission || !$permission->isAuthorized()) {
            throw $this->createAccessDeniedException('You are not authorized to access this site.');
        }

        // Logique pour afficher le site
        return $this->render('site/show.html.twig', [
            'site' => $site,
        ]);
    }
   

}
