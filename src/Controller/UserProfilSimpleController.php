<?php

namespace App\Controller;
use App\Entity\LoginSite;
use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class UserProfilSimpleController extends AbstractController
{
    #[Route('/user/profil/simple', name: 'app_user_profil_simple')]
    public function index(UserRepository $userRepository , SiteRepository $siteRepository): Response

    {
        $users = $userRepository->findAll();
        $sites = $siteRepository->findAll();
        return $this->render('user_profil_simple/index.html.twig', [
            'users' => $users,
            'sites' =>$sites
        ]);
    }
    #[Route("/site/login_simple/password/{siteId}", name:"login_simple_and_password", methods:["POST"])]
public function login_and_password(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer les données du formulaire
    $nameSite = $request->request->get('nameSite');
    $login = $request->request->get('login');
    $password = $request->request->get('password');
    $userId = $request->request->get('userId');
    $siteId = $request->request->get('siteId');

    // Trouver l'utilisateur et le site par l'ID
    $user = $entityManager->getRepository(User::class)->find($userId);
    $site = $entityManager->getRepository(Site::class)->find($siteId);
    if ($password) {
        // Clé de chiffrement (stockée dans le .env)
        $encryptionKey = hex2bin($_ENV['ENCRYPTION_KEY']); // définir cette clé dans .env

        // Vecteur d'initialisation (IV)
        $ivLength = 16 ;//openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength); // Générer un IV
        $encoded_iv = base64_encode($iv);
        // Chiffrement symétrique du mot de passe
    
        $cipherPassword = openssl_encrypt($password, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA, $iv);

        // Stocker le IV avec le mot de passe chiffré pour pouvoir le déchiffrer plus tard
        $cipherPasswordWithIV = $encoded_iv . "::" . base64_encode($iv . $cipherPassword);
    }

    // Créer une nouvelle entité LoginSite
    $loginSite = new LoginSite();
    $loginSite->setNameSite($nameSite);
    $loginSite->setLogin($login);
    $loginSite->setMdp($cipherPasswordWithIV); // Stocker le mot de passe chiffré avec l'IV
    $loginSite->setUser($user);
    $loginSite->setSite($site);

    // Enregistrer les données dans la base de données
    $entityManager->persist($loginSite);
    $entityManager->flush();

    // Rediriger vers une page de confirmation ou afficher un message
    return $this->redirectToRoute('app_user_profil_simple');
}

}
