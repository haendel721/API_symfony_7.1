<?php 

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class loginController extends AbstractController
{
    /**
     * @throws \JsonException
     */
    #[Route(path: "/api/login" , name: "api_login" , methods: ["POST"])]
    public function ApiLogin(){
        $user = $this->getUser();

        $userdata = [
            'rôle'=>$user->getRoles(),
            'email' =>$user->getEmail(),
            'nom' =>$user->getName(),
            'prenom' =>$user->getSurname(),
            
        ];
        return $this->json($userdata);
        // return new JsonResponse(json_encode($userdata,JSON_THROW_ON_ERROR));
    }
}