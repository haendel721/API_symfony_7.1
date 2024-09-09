<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\SiteType;
use App\Repository\PermissionRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/site')]
class SiteController extends AbstractController
{
    #[Route('/', name: 'app_site_index', methods: ['GET'])]
    public function index(SiteRepository $siteRepository , PermissionRepository $permissionRepository ): Response
    {
       
        return $this->render('site/index.html.twig', [
            'sites' => $siteRepository->findAll(),
            'permissions' => $permissionRepository->findAll(),
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
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

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
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
