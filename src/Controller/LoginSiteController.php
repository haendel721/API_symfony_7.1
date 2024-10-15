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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
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
        // Récupérer le nouveau mot de passe depuis le formulaire
        $newPassword = $form->get('password')->getData();

        // Vérifier si un nouveau mot de passe est fourni
        if ($newPassword) {
            // Clé de chiffrement (stockée dans le .env sous forme hexadécimale)
            $encryptionKey = hex2bin($_ENV['ENCRYPTION_KEY']);

            // Vecteur d'initialisation (IV)
            $ivLength = 16; // Longueur pour AES-256-CBC
            $iv = openssl_random_pseudo_bytes($ivLength); // Générer un IV aléatoire
            $encoded_iv = base64_encode($iv);

            // Chiffrement symétrique du mot de passe
            $cipherPassword = openssl_encrypt($newPassword, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA, $iv);

            // Stocker le IV avec le mot de passe chiffré pour le déchiffrement ultérieur
            $cipherPasswordWithIV = $encoded_iv . "::" . base64_encode($cipherPassword);

            // Remplacer l'ancien mot de passe par le nouveau mot de passe chiffré
            $loginSite->setMdp($cipherPasswordWithIV);
        } else {
            // Si aucun nouveau mot de passe n'est fourni, l'ancien mot de passe est conservé
        }

        // Pas besoin de créer un nouvel objet, car on travaille sur l'existant
        $entityManager->flush();
        $this->addFlash('success', 'Modification avec succès');
        
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


    
    #[Route('/api/login/json', name: 'app_json_afficher', methods: ['GET'])]
public function jsonindex(LoginSiteRepository $loginSiteRepository ,SiteRepository $siteRepository): JsonResponse
{
    $loginSites = $loginSiteRepository->findAll();
    $logindata = [];
    foreach ($loginSites as $loginSite) {
        $site = $loginSite->getSite();  // Vérifiez d'abord si l'objet Site existe
        $logindata[] = [
            'id' => $loginSite->getId(),
            'nom' => $loginSite->getNameSite(),
            'login' => $loginSite->getLogin(),
            'password' => $loginSite->getMdp(),
            'url' => $site ? $site->getUrl() : '',  // Si $site est null, mettez 'N/A' ou autre valeur par défaut
            'id-login' => $site ? $site->getIdLogin() : '',
            'class-login' => $site ? $site->getClassLogin() : '',
            'id-mdp' => $site ? $site->getIdMdp() : '',
            'class-mdp' => $site ? $site->getClassMdp() : '',
            'class-submit' => $site ? $site->getClassSubmit() : '',
        ];
    }

    return new JsonResponse($logindata);
}


}