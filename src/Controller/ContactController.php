<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Utils;
use App\Service\Messagerie;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }

    #[Route('/contact/create', name: 'app_contact_create')]
    public function contactCreate(EntityManagerInterface $em, ContactRepository $repo,
    Request $request, SluggerInterface $slugger, Messagerie $messagerie):Response
    {   
        $msg = "";
        //Instancier un objet contact
        $contact = new Contact();
        //instancier un objet formulaire
        $form = $this->createForm(ContactType::class, $contact);
        //récupérer les données
        $form->handleRequest($request);
        //test si le formulaire est submit
        if($form->isSubmitted()){
            //nettoyage des inputs
            $name = Utils::cleanInputStatic($request->request->all('contact')['name']);
            $email = Utils::cleanInputStatic($request->request->all('contact')['email']);
            $phone = Utils::cleanInputStatic($request->request->all('contact')['phone']);
            $content = Utils::cleanInputStatic($request->request->all('contact')['content']);
            //set des attributs nettoyé
            $contact->setName($name);
            $contact->setEmail($email);
            $contact->setPhone($phone);
            $contact->setContent($content);
            $contact->setDate(new \DateTimeImmutable());
            
            $file = $form->get('file')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('contact_uploads'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $contact->setFile($newFilename);
            }
            
            //persister les données
            $em->persist($contact);
            //ajoute en BDD
            $em->flush();
            $msg = "Le compte : ".$contact->getEmail()." a été ajouté en BDD";

            // récupération des ID de messagerie
            $login=$this->getParameter('login');
            $mdp=$this->getParameter('mdp');

            // variable pour le mail
            $objet = 'Nouvelle demande de contact';
            $content = '<p>Nom : '.mb_convert_encoding($contact->getName(), 'ISO-8859-1', 'UTF-8').'</br>'.
            'Mail : '.$contact->getEmail().'</br>'.
            'Message : '.mb_convert_encoding($contact->getContent(), 'ISO-8859-1', 'UTF-8').'</p>';
            $destinataire = 'enora.lafforgue@gmail.com';
            
            // on stocke la fonction dans une variable
            $statut = $messagerie->sendEmail($login, $mdp, $destinataire, $objet, $content);
        }
        return $this->render('contact/form.html.twig', [
            'msg'=> $msg,
            'form'=> $form->createView(),
        ]);
    }

    #[Route('/admin/contact/all', name:'app_admin_contact_all')]
    public function showAllContact(ContactRepository $contactRepository):Response{
        //récuperer dans un tableau tous les articles
        $contacts = $contactRepository->findAll();
        return $this->render('contact/index.html.twig', [
            'list'=> $contacts,
        ]);
    }

    #[Route('/admin/contact/delete/{id}', name:'app_admin_contact_delete')]
    public function contactDelete (int $id, ContactRepository $repo,
    EntityManagerInterface $em){
        $contact = $repo->find($id);
        $em->remove($contact);
        $em->flush();
        return $this->redirectToRoute('app_admin_contact_all');
    }
}
