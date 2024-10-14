<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\Site;
use App\Entity\User;
use App\Form\Permission1Type;
use App\Repository\PermissionRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/permission')]
class PermissionController extends AbstractController
{
    #[Route('/', name: 'app_permission_index', methods: ['GET'])]
    public function index(PermissionRepository $permissionRepository ): Response
    {
        $permissions = $permissionRepository->findAllOrderedByName();
        return $this->render('permission/index.html.twig', [
            'permissions' => $permissions,
        ]);
    }
    #[Route('/api/liste/permission/json', name: 'app_liste_permission_json_afficher', methods: ['GET'])]
    public function permissionlistejsonindex(PermissionRepository $permissionRepository): JsonResponse
    {
        $permission = $permissionRepository->findAll();
        $permissiondata = [];
        foreach ($permission as $permissions) {
            $permissiondata[] = [
                'id' => $permissions->getId(),
                'créer le ' => $permissions->getCreatedAt(),
                'autorisation' => $permissions->isAuthorized(),
                'utilisateur' => $permissions->getUser()->getName(),
                'site' => $permissions->getSite()->getName(),
            ];
        }

        return new JsonResponse($permissiondata);
    }
    #[Route('/new', name: 'app_permission_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $permission = new Permission();
        $form = $this->createForm(Permission1Type::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // recuperer l'utilisateur
            $user = $permission->getUser();
            $site = $permission->getSite();

            // Rechercher s'il existe déjà une permission pour cet utilisateur et ce site
            $permissionExiste = $entityManager->getRepository(Permission::class)->findOneBy([
                'user'=>$user,
                'site'=>$site
            ]);

            if($permissionExiste){
                $this->addFlash('warning', 'La permission pour ' . $site->getName() . ' avec l\'utilisateur ' . $user->getName() . ' existe déjà.');
                return $this->redirectToRoute('app_permission_new', [], Response::HTTP_SEE_OTHER);
            }
            // Sinon, on persiste la nouvelle permission
            $entityManager->persist($permission);
            $entityManager->flush();
            $this->addFlash('succes','Ajout du ' . $permission->getSite()->getName() . ' pour l\'user '. $permission->getUser()->getName() . '  avec succès');
            return $this->redirectToRoute('app_permission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('permission/new.html.twig', [
            'permission' => $permission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_permission_show', methods: ['GET'])]
    public function show(Permission $permission): Response
    {
        return $this->render('permission/show.html.twig', [
            'permission' => $permission,
        ]);
        
    }

    #[Route('/{id}/edit', name: 'app_permission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Permission $permission, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Permission1Type::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info','Modification  de  ' . $permission->getSite()->getName() . ' pour '. $permission->getUser()->getName() . '   avec succès');

            return $this->redirectToRoute('app_permission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('permission/edit.html.twig', [
            'permission' => $permission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_permission_delete', methods: ['POST'])]
    public function delete(Request $request, Permission $permission, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$permission->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($permission);
            $entityManager->flush();
            $this->addFlash('danger','Suppression  de  ' . $permission->getSite()->getName() . ' pour '. $permission->getUser()->getName() . '   avec succès');
        }

        return $this->redirectToRoute('app_permission_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/suppimer', name: 'supprimer_permission_direct')]
    public function supprimer(Permission $permission, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($permission);
            $entityManager->flush();
            
            $this->addFlash('danger','Suppression  de  ' . $permission->getSite()->getName() . ' pour '. $permission->getUser()->getName() . '   avec succès');
            return $this->redirectToRoute('app_permission_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/assign-permission/{permissionId}', name: 'assign_permission')]
    public function assignPermission(int $permissionId, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Récupérer l'entité à mettre à jour
        $permission = $entityManager->getRepository(Permission::class)->find($permissionId);
    
        if (!$permission) {
            throw $this->createNotFoundException('Permission not found.');
        }
    
        // Mettre à jour l'autorisation
        $permission->setAuthorized(true);
    
        // Enregistrer les modifications
        $entityManager->flush();
        $this->addFlash('success','assignation de  ' . $permission->getSite()->getName() . ' pour '. $permission->getUser()->getName() . '  avec succès');
    
        // Rediriger vers la page précédente
        return $this->redirectToRoute('app_permission_index');
    }
    
    #[Route('/retire-permission/{permissionId}', name: 'retire_permission')]
    public function retirePermission(int $permissionId, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Récupérer l'entité à mettre à jour
        $permission = $entityManager->getRepository(Permission::class)->find($permissionId);
    
        if (!$permission) {
            throw $this->createNotFoundException('Permission not found.');
        }
    
        // Mettre à jour l'autorisation
        $permission->setAuthorized(false);
    
        // Enregistrer les modifications
        $entityManager->flush();
        $this->addFlash('danger','Retrait  de  ' . $permission->getSite()->getName() . ' pour '. $permission->getUser()->getName() . '   avec succès');
    
        // Rediriger vers la page précédente
        return $this->redirectToRoute('app_permission_index');
    }
}
