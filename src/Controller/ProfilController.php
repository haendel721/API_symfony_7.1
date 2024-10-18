<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/admin/profil/user')]
final class ProfilController extends AbstractController
{
    #[Route('/',name: 'app_profil_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('profil/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_profil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profil_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('profil/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $passwordEncoder , EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
             // Vérifier si un nouveau mot de passe a été soumis
            $plainPassword = $form->get('password')->getData();
            
            if (!empty($plainPassword)) {
                // Encodage du mot de passe
                $encodedPassword = $passwordEncoder->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);
            }
             // Sauvegarde des autres informations utilisateur
        $entityManager->flush();

            return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    public function showUser(UserRepository $userRepository, int $id)
{
    $user = $userRepository->find($id);

    return $this->render('user/show.html.twig', [
        'user' => $user,
    ]);
}
    #[Route('/{id}/delete', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
            $permission = $entityManager->getRepository(Permission::class)->findBy(['user'=>$user]);

            foreach($permission as $permissions){
                $entityManager->remove($permissions);
            }
            
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('danger', 'Suppression  de  ' . $user->getName() . ' pour '. $user->getName() . '  avec succes');

        return $this->redirectToRoute('app_profil_index');
    }


}



