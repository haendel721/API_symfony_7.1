<?php

namespace App\Controller;

use App\Entity\LoginSite;
use App\Form\LoginSiteType;
use App\Repository\LoginSiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/log')]
final class LogController extends AbstractController
{
    #[Route(name: 'app_log_index', methods: ['GET'])]
    public function index(LoginSiteRepository $loginSiteRepository): Response
    {
        return $this->render('log/index.html.twig', [
            'login_sites' => $loginSiteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $loginSite = new LoginSite();
        $form = $this->createForm(LoginSiteType::class, $loginSite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($loginSite);
            $entityManager->flush();

            return $this->redirectToRoute('app_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('log/new.html.twig', [
            'login_site' => $loginSite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_log_show', methods: ['GET'])]
    public function show(LoginSite $loginSite): Response
    {
        return $this->render('log/show.html.twig', [
            'login_site' => $loginSite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LoginSite $loginSite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LoginSiteType::class, $loginSite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe depuis le formulaire
            $password = $form->get('mdp')->getData(); // Utilisez le formulaire pour récupérer les données
    
            // Vérifiez si un nouveau mot de passe est fourni
            if ($password) {
                // Clé de chiffrement (stockée dans le .env)
                $encryptionKey = $_ENV['ENCRYPTION_KEY']; // Assurez-vous que cette clé est définie dans le .env
    
                // Vecteur d'initialisation (IV)
                $ivLength = openssl_cipher_iv_length('aes-256-cbc');
                $iv = openssl_random_pseudo_bytes($ivLength); // Générer un IV
    
                // Chiffrement symétrique du mot de passe
                $cipherPassword = openssl_encrypt($password, 'aes-256-cbc', $encryptionKey, 0, $iv);
    
                // Stocker le IV avec le mot de passe chiffré
                $cipherPasswordWithIV = base64_encode($iv . $cipherPassword);
                $loginSite->setMdp($cipherPasswordWithIV); // Mettez à jour le mot de passe chiffré
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('log/edit.html.twig', [
            'login_site' => $loginSite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_log_delete', methods: ['POST'])]
    public function delete(Request $request, LoginSite $loginSite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$loginSite->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($loginSite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_log_index', [], Response::HTTP_SEE_OTHER);
    }
}
