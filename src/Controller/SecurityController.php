<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $session = $request->getSession();
        if ($session->get('logged_out')) {
            return $this->render('security/login.html.twig', [
                'error' => 'Votre session a été invalidée. Vous devez utiliser le bouton de déconnexion pour accéder à nouveau.',
            ]);
        }
        // if ($this->getUser()) {
            
        //     // dump($this->getUser());
        //     $roles = $this->getUser()->getRoles();
        //     if (in_array('ROLE_ADMIN', $roles, true)) {
        //         return $this->redirectToRoute('app_profil_index');
        //     } elseif (in_array('ROLE_USER', $roles, true)) {
        //         return $this->redirectToRoute('app_user_profil_simple');
        //     } else {
        //         return $this->redirectToRoute('app_user_profil_simple');
        //     }
            
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
   

    #[Route("/logout", name:"app_logout")]
    public function logout(Request $request): RedirectResponse
    {
        // Marquer la session comme nécessitant une déconnexion
        $session = $request->getSession();
        $session->set('logged_out', true);

        // Symfony gère la déconnexion ici
        return $this->redirectToRoute('app_homepage'); // Redirection après déconnexion
    }
}
