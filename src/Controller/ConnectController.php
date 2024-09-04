<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConnectController extends AbstractController
{
    #[Route('/connect', name: 'app_connect')]
    public function index(): Response
    {
        return $this->render('connect/index.html.twig', [
            'controller_name' => 'ConnectController',
        ]);
    }
    
    #[Route("/connect/{service}", name:"connect_service_start")]
     
    public function redirectToService(ClientRegistry $clientRegistry, $service): Response
    {
        return $clientRegistry->getClient($service)->redirect();
    }

    
    #[Route("/connect/{service}/check", name:"connect_service_check")]
     
    public function serviceCheck( ClientRegistry $clientRegistry, $service): Response
    {
        $client = $clientRegistry->getClient($service);

        try {
            // Obtenez l'utilisateur OAuth et le token
            $user = $client->fetchUser();
            $accessToken = $client->getAccessToken();

            // Gérer l'utilisateur ou le token (par exemple, enregistrer dans la session)
            // Rediriger vers le site cible
            return $this->redirect('https://' . $service . '.com');
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_site_index');
        }
    }
}
