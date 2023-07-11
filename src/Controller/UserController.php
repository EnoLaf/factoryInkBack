<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Utils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/admin/register', name: 'app_admin_register')]
    public function userAdd(EntityManagerInterface $em, UserRepository $repo,
    Request $request, UserPasswordHasherInterface $hash):Response
    {   
        $msg = "";
        //Instancier un objet User
        $user = new User();
        //instancier un objet formulaire
        $form = $this->createForm(UserType::class, $user);
        //récupérer les données
        $form->handleRequest($request);
        //test si le formulaire est submit
        if($form->isSubmitted()){
            //récupération du password
            $pass = Utils::cleanInputStatic($request->request->all('user')['password']);
            //hashage du password
            $hash = $hash->hashPassword($user, $pass);
            //nettoyage des inputs
            $lastName = Utils::cleanInputStatic($request->request->all('user')['lastName']);
            $firstName = Utils::cleanInputStatic($request->request->all('user')['firstName']);
            $email = Utils::cleanInputStatic($request->request->all('user')['email']);
            //set des attributs nettoyé
            $user->setPassword($hash);
            $user->setLastName($lastName);
            $user->setFirstName($firstName);
            $user->setEmail($email);

            //récupération d'un compte utilisateur
            $recup = $repo->findOneBy(['email'=>$user->getEmail()]);
            //tester si le compte existe
            if($recup){
                $msg = "Le compte : ".$user->getEmail()." existe déja";
            }
            else{
                //persister les données
                $em->persist($user);
                //ajoute en BDD
                $em->flush();
            }

            $msg = "Le compte : ".$user->getEmail()." a été ajouté en BDD";
        }
        return $this->render('user/register.html.twig', [
            'msg'=> $msg,
            'form'=> $form->createView(),
        ]);
    }

    
}
