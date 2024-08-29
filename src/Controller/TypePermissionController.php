<?php

namespace App\Controller;

use App\Entity\TypePermission;
use App\Form\TypePermissionType;
use App\Repository\TypePermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/type/permission')]
class TypePermissionController extends AbstractController
{
    #[Route('/', name: 'app_type_permission_index', methods: ['GET'])]
    public function index(TypePermissionRepository $typePermissionRepository): Response
    {
        return $this->render('type_permission/index.html.twig', [
            'type_permissions' => $typePermissionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_permission_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typePermission = new TypePermission();
        $form = $this->createForm(TypePermissionType::class, $typePermission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typePermission);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_permission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_permission/new.html.twig', [
            'type_permission' => $typePermission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_permission_show', methods: ['GET'])]
    public function show(TypePermission $typePermission): Response
    {
        return $this->render('type_permission/show.html.twig', [
            'type_permission' => $typePermission,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_permission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypePermission $typePermission, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypePermissionType::class, $typePermission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_permission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_permission/edit.html.twig', [
            'type_permission' => $typePermission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_permission_delete', methods: ['POST'])]
    public function delete(Request $request, TypePermission $typePermission, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typePermission->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typePermission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_permission_index', [], Response::HTTP_SEE_OTHER);
    }
}
