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
use App\Service\Messagerie;
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
    Request $request, UserPasswordHasherInterface $hash, Messagerie $messagerie):Response
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
            $user->setRoles(["ROLE_ADMIN"]);
            $user->setAdmin(false);

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

            //récupération des ID de messagerie
            $login=$this->getParameter('login');
            $mdp=$this->getParameter('mdp');

            //variable pour le mail
            $objet = 'Activation d\'un compte admin';
            $content = '<p>L\'utilisateur : '.mb_convert_encoding($user->getLastName(), 'ISO-8859-1', 'UTF-8').' '
            .mb_convert_encoding($user->getFirstName(), 'ISO-8859-1', 'UTF-8').' souhaite créer un compte administrateur.'.
            'Pour activer le compte admin veuillez cliquer sur l\'url ci-dessous:</p>'.
            '<a href="https://127.0.0.1:8000/admin/activate/'.$user->getId().'">Accepter</a>'.
            '<a href="https://127.0.0.1:8000/register/delete/'.$user->getId().'">Refuser</a>';

            $destinataire = 'enora.lafforgue@gmail.com';

            //on stocke la fonction dans une variable
            $statut = $messagerie->sendEmail($login, $mdp, $destinataire, $objet, $content);

            $msg = "Un mail a été envoyé à un admin pour accepter votre demande.";
        }
        return $this->render('user/register.html.twig', [
            'msg'=> $msg,
            'form'=> $form->createView(),
        ]);
    }

    #[Route('/admin/activate/{id}', name: 'app_admin_activate')]
    public function userActivate(EntityManagerInterface $em, UserRepository $userRepository, int $id):Response{  
        //récupérer user par son id 
        $user = $userRepository->find($id);

        //passer actiavate à 1
        $user->setAdmin(true);
        
        if($user){
            //persister les données
            $em->persist($user);
            //ajoute en BDD
            $em->flush();
            //rediriger ver la connexion
            return $this->redirectToRoute('app_admin_login');
        }else{  //sinon le compte n'existe pas
            //rediriger ver la connexion
            return $this->redirectToRoute('app_admin_register');
        }

        return $this->render('user/index2.html.twig', [
        ]);
    }

    #[Route('/admin/user/delete/{id}', name:'app_admin_user_delete')]
    public function userDelete (int $id, UserRepository $repo,
    EntityManagerInterface $em){
        $user = $repo->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_admin_home');
    }
    
}
