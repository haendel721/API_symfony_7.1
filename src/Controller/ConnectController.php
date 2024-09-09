<?php

namespace App\Controller;

use App\Entity\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class ConnectController extends AbstractController
{
//     #[Route('/connect', name: 'app_connect')]
//     public function index(): Response
//     {
//         return $this->render('connect/index.html.twig', [
//             'controller_name' => 'ConnectController',
//         ]);
//     }
//     #[Route("/connect/facebook", name:"connect_facebook_start")]
    
//    public function connectAction(ClientRegistry $clientRegistry)
//    {
//        // Redirige l'utilisateur vers Facebook
//        return $clientRegistry
//            ->getClient('facebook')
//            ->redirect(['email'], ['public_profile']);
//         //    ->redirect([
//         //     'email',            // Demande l'email de l'utilisateur
//         //     'public_profile',   // Demande l'accès au profil public de l'utilisateur
//             // Ajoute d'autres scopes si nécessaire, par exemple:
//             //'user_friends',   // Accès aux amis de l'utilisateur
//         // ]);
//         }

//    /**
//     * @Route("/connect/facebook/check", name="connect_facebook_check")
//     */
//    public function connectCheckAction(Request $request)
//    {
//        // Cette méthode sera appelée après que Facebook a renvoyé l'utilisateur.
//        // Vous pouvez récupérer les informations de l'utilisateur connecté ici.
//        $client = $this->get('knpu.oauth2.client.facebook');
//        $user = $client->fetchUser();
       
//        // Vous pouvez maintenant gérer l'utilisateur connecté (création de compte, connexion, etc.)
//    }
//     #[Route("/connect/{service}", name:"connect_service_start")]
     
//     public function redirectToService(ClientRegistry $clientRegistry, $service): Response
//     {
//         return $clientRegistry
//             ->getClient($service)
//             ->redirect(['email'],[]);
//     }

    
    // #[Route("/connect/{service}/check", name:"connect_service_check")]
     
    // public function serviceCheck( ClientRegistry $clientRegistry, $service): Response
    // {
    //     $client = $clientRegistry->getClient($service);

    //     try {
    //         // Obtenez l'utilisateur OAuth et le token
    //         $user = $client->fetchUser();
    //         $accessToken = $client->getAccessToken();
    //         // Obtenir l'URL du profil via l'API Graph
    //         $graphResponse = $client->fetchUserFromToken($accessToken);
    //         $profileUrl = $graphResponse->getLink();

    //         // Rediriger vers le profil Facebook de l'utilisateur
    //         return $this->redirect($profileUrl);
    //     } catch (\Exception $e) {
    //         return $this->redirectToRoute('app_site_index');
    //     }
    // }
    // tsy mande
    // public function connectServiceCheck($service ,Request $request, ClientRegistry $clientRegistry, JWTEncoderInterface $jwtEncoder, AuthenticationUtils $authenticationUtils)
    // {
    //     // Attempt to authenticate the user with Facebook
    //     $client = $clientRegistry->getClient($service);
    //     try {
    //         $accessToken = $client->fetchUserFromToken($request);
    //     } catch (\Exception $e) {
    //         // Handle authentication errors
    //         $authenticationUtils->getLastAuthenticationError();
    //         return $this->redirectToRoute('connect_facebook');
    //     }

    //     // Get user information from Facebook's Graph API
    //     $graphClient = $client->getAccessToken()->getToken();
    //     $response = $graphClient->sendRequest('GET', '/me?fields=id,name,email');
    //     $userData = $response->getContent();

    //     // Create a custom user object with the retrieved information
    //     $user = new User(); // Replace with your user entity
    //     // $user->setFacebookId($userData['id']);
    //     $user->setName($userData['name']);
    //     $user->setEmail($userData['email']);

    //     // Generate a JWT token for the user
    //     $jwtToken = $jwtEncoder->encode([
    //         'username' => $user->getEmail(),
    //         'roles' => ['ROLE_USER'], // Adjust roles as needed
    //     ]);

    //     // Log the user in with the JWT token
    //     // $authenticationUtils->setLastAuthenticationError();
    //     $token = new UsernamePasswordToken($user, $jwtToken, 'main', $user->getRoles());
    //     $this->get('security.token_storage')->setToken($token);

    //     return $this->redirectToRoute('homepage'); // Replace with your desired redirect route
    // }
}
