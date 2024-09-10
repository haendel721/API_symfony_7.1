<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\Site;
use App\Entity\User;
use App\Form\Permission1Type;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager , SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
       
        
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            
            );
            if($image){
                $originalName = pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                $newFileName = $safeFileName .'_'. uniqid() . '.' . $image->guessExtension();
                
                try{
                    $image->move(
                        $this->getParameter('image_dir'),
                        $newFileName
                    );
                }catch(FileException $exception){}
                $user->setImage($newFileName);
            // dump($user->getImage());
                
            }
            
            $entityManager->persist($user);
            $entityManager->flush();
        
            $sites = $entityManager->getRepository(Site::class)->findAll();
            // Ajouter automatiquement une permission "non autorisée" pour chaque site
            foreach ($sites as $site) {
                $permission = new Permission();
                $permission->setUser($user);
                $permission->setSite($site);
                $permission->setAuthorized(false); // "Non autorisé"

                // Persister la permission pour chaque site
                $entityManager->persist($permission);
            }

            $entityManager->flush();
                
            $this->addFlash('succes', 'Utilisateur enregistré avec succès');
            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            // return $security->login($user, UserAuthenticator::class, 'main');

            
        }
        return $this->render('registration/register.html.twig', [
        'registrationForm' => $form,
    ]);
        
    }
    
}
