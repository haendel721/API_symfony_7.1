<?php

namespace App\Controller;

use App\Entity\CategorySite;
use App\Form\CategorySiteType;
use App\Repository\CategorySiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/category/site')]
class CategorySiteController extends AbstractController
{
    #[Route('/', name: 'app_category_site_index', methods: ['GET'])]
    public function index(CategorySiteRepository $categorySiteRepository): Response
    {
        return $this->render('category_site/index.html.twig', [
            'category_sites' => $categorySiteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_site_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorySite = new CategorySite();
        $form = $this->createForm(CategorySiteType::class, $categorySite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorySite);
            $entityManager->flush();
            $this->addFlash('success', 'Ajout  de  ' . $categorySite->getName() . ' pour '. $categorySite->getName() . '  avec succes');

            return $this->redirectToRoute('app_category_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category_site/new.html.twig', [
            'category_site' => $categorySite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_site_show', methods: ['GET'])]
    public function show(CategorySite $categorySite): Response
    {
        return $this->render('category_site/show.html.twig', [
            'category_site' => $categorySite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_site_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorySite $categorySite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorySiteType::class, $categorySite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', 'Modification  de  ' . $categorySite->getName() . ' pour '. $categorySite->getName() . '  avec succes');

            return $this->redirectToRoute('app_category_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category_site/edit.html.twig', [
            'category_site' => $categorySite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_site_delete', methods: ['POST'])]
    public function delete(Request $request, CategorySite $categorySite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorySite->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorySite);
            $entityManager->flush();
            $this->addFlash('danger', 'Ajout  de  ' . $categorySite->getName() . ' pour '. $categorySite->getName() . '  avec succes');
        }

        return $this->redirectToRoute('app_category_site_index', [], Response::HTTP_SEE_OTHER);
    }
}
