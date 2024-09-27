<?php

namespace App\Controller;

use App\Entity\LoginSite;
use App\Entity\Site;
use App\Entity\User;
use App\Form\LoginSiteType;
use App\Repository\LoginSiteRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;

class LoginSiteController extends AbstractController
{
    private $encryptionKey;

    public function __construct()
    {
        // Définis la clé de chiffrement. Il est recommandé de l'extraire de l'environnement
        $this->encryptionKey = hex2bin($_ENV['ENCRYPTION_KEY']); // Clé définie dans .env
    }

    // #[Route("/encrypt/{password}", name:"encryption_encrypt")]
    // public function encrypt(string $password): Response
    // {
    //     // Générer un vecteur d'initialisation (IV)
    //     $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        
    //     // Chiffrer le mot de passe
    //     $encryptedPassword = openssl_encrypt($password, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
        
    //     // Combiner l'IV et le mot de passe chiffré et les encoder en base64
    //     $encryptedPasswordWithIv = base64_encode($iv . $encryptedPassword);

    //     return new Response("Mot de passe chiffré : $encryptedPasswordWithIv");
    // }
    #[Route("/decrypt/{encryptedPasswordWithIv}", name:"encryption_decrypt")]
    function decryptPassword($encryptedPassword, $encryptionKey)
    {
        // Décoder la chaîne base64 pour récupérer l'IV et le mot de passe chiffré
        $data = base64_decode($encryptedPassword);
        
        // Récupérer la longueur de l'IV
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        
        // Extraire l'IV
        $iv = substr($data, 0, $ivLength);
        
        // Extraire le mot de passe chiffré
        $cipherPassword = substr($data, $ivLength);
        
        // Déchiffrer le mot de passe
        $decryptedPassword = openssl_decrypt($cipherPassword, 'aes-256-cbc', $encryptionKey, 0, $iv);
        
        return $decryptedPassword;
    }

    
    #[Route("/site/login/password/{siteId}", name:"login_and_password", methods:["POST"])]
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

    // Clé de chiffrement (stockée dans le .env)
    $encryptionKey = $_ENV['ENCRYPTION_KEY']; // définir cette clé dans .env

    // Vecteur d'initialisation (IV)
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivLength); // Générer un IV

    // Chiffrement symétrique du mot de passe
    if ($password) {
        $cipherPassword = openssl_encrypt($password, 'aes-256-cbc', $encryptionKey, 0, $iv);

        // Stocker le IV avec le mot de passe chiffré pour pouvoir le déchiffrer plus tard
        $cipherPasswordWithIV = base64_encode($iv . $cipherPassword);
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
    return $this->redirectToRoute('app_profil');
}

    #[Route('/LoginSite', name: 'login_site')]
    public function index(Request $request , LoginSite $loginSite,LoginSiteRepository $loginSiteRepository): Response
    {
        $form = $this->createForm(LoginSiteType::class, $loginSite);
        $form->handleRequest($request);
        return $this->render('login_site/index.html.twig', [
            'loginSites' => $loginSiteRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
    // mbola tsy mety #[Route('/{id}/{Id}/delete', name: 'app_login_delete')]
    // public function delete(Site $site, User $user , EntityManagerInterface $entityManager): Response
    // {
    //         $loginSite = $entityManager->getRepository(LoginSite::class)
    //                                    ->findBy([
    //                                         'site'=> $site , 
    //                                         'user' => $user
    //                                     ] );

    //         foreach($loginSite as $loginSites){
    //             $entityManager->remove($loginSites);
    //         }
            
    //         $entityManager->remove($loginSites);
    //         $entityManager->flush();
    //         $this->addFlash('danger', 'Suppression avec succes');

    //     return $this->redirectToRoute('login_site');
    // }

    #[Route('/{id}/edit', name: 'app_login_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SiteRepository $siteRepository, LoginSite $loginSite, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire avec l'entité existante
        $form = $this->createForm(LoginSiteType::class, $loginSite);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe depuis le formulaire
            $password = $form->get('password')->getData();
    
            // Vérifiez si un nouveau mot de passe est fourni
            if ($password) {
                // Chiffrement du mot de passe avec la clé et l'IV
                $encryptionKey = $_ENV['ENCRYPTION_KEY']; // Assurez-vous que cette clé est définie dans le .env
                $ivLength = openssl_cipher_iv_length('aes-256-cbc');
                $iv = openssl_random_pseudo_bytes($ivLength);
                $cipherPassword = openssl_encrypt($password, 'aes-256-cbc', $encryptionKey, 0, $iv);
                $cipherPasswordWithIV = base64_encode($iv . $cipherPassword);
    
                $loginSite->setMdp($cipherPasswordWithIV); // Mettre à jour le mot de passe chiffré
            }
    
            // Pas besoin de créer un nouvel objet, car on travaille sur l'existant
            $entityManager->flush();
    
            // Redirection après modification
            return $this->redirectToRoute('app_log_index', [], Response::HTTP_SEE_OTHER);
        }
    
        // Afficher le formulaire avec les données actuelles
        return $this->render('user/index.html.twig', [
            'loginSite' => $loginSite,
            'sites' => $siteRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
    
    


}