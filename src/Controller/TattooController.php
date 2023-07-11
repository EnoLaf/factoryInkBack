<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tattoo;
use App\Entity\Artist;
use App\Repository\TattooRepository;
use App\Repository\ArtistRepository;
use App\Form\TattooType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Utils;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class TattooController extends AbstractController
{
    #[Route('/tattoo', name: 'app_tattoo')]
    public function index(): Response
    {
        return $this->render('tattoo/index.html.twig', [
            'controller_name' => 'TattooController',
        ]);
    }

    #[Route('/admin/tattoo/create', name: 'app_admin_tattoo_create')]
    public function tattooCreate(EntityManagerInterface $em, Request $request, 
    SluggerInterface $slugger):Response
    {   
        $msg = "";
        //Instancier un objet contact
        $tattoo = new Tattoo();
        //instancier un objet formulaire
        $form = $this->createForm(TattooType::class, $tattoo);
        //récupérer les données
        $form->handleRequest($request);
        //test si le formulaire est submit
        if($form->isSubmitted()){
            //nettoyage des inputs
            $flash = Utils::cleanInputStatic($request->request->all('tattoo')['flash']);
            $artist = new Artist($request->request->all('tattoo')['artist']);
            if(Utils::cleanInputStatic($request->request->all('tattoo')['price'])==''){
                $tattoo->setPrice(null);
            }else{
                $tattoo->setPrice(Utils::cleanInputStatic(Utils::cleanInputStatic($request->request->all('tattoo')['price'])));
            }
            //set des attributs nettoyé
            $tattoo->setFlash($flash);
            $tattoo->setArtist($artist);
            
            $file = $form->get('url')->getData();
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
                        $this->getParameter('gallery_uploads'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $tattoo->setUrl($newFilename);
            }

            //persister les données
            $em->persist($tattoo);
            //ajoute en BDD
            $em->flush();

        }
        return $this->render('tattoo/form.html.twig', [
            'msg'=> $msg,
            'form'=> $form->createView(),
        ]);
    }

    #[Route('/admin/tattoo/all', name:'app_admin_tattoo_all')]
    public function showAllTattoo(TattooRepository $tattooRepository):Response{
        //récuperer dans un tableau tous les articles
        $tattoos = $tattooRepository->findAll();
        $tattoosShop = $tattooRepository->findBy(['flash' => false]);
        $tattoosFlash = $tattooRepository->findBy(['flash' => true]);
        return $this->render('tattoo/index.html.twig', [
            'list' => $tattoos,
            'listShop' => $tattoosShop,
            'listFlash' => $tattoosFlash,
        ]);
    }

    #[Route('/admin/tattoo/artist/{id}', name:'app_admin_tattoo_artist')]
    public function showTattooArtist(int $id, ArtistRepository $artistRepository):Response{
        //récuperer dans un tableau tous les articles
        $gallery = $artistRepository->find($id);
        return $this->render('tattoo/artist.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/admin/tattoo/update/{id}', name:'app_admin_tattoo_update')]
    public function updateTattoo(int $id, TattooRepository $repo,
    EntityManagerInterface $em, Request $request,){
        $msg = "";
        //Récupérer la catégorie
        $tattoo = $repo->find($id);
        //créer le formulaire
        $form = $this->createForm(TattooType::class, $tattoo);
        $form->remove('url');
        //Récupération des datas du formulaire
        $form->handleRequest($request);
        //tester si le formulaire est submit
        if($form->isSubmitted() AND $form->isValid()){
            //persister les données du formulaire
            $em->persist($tattoo);
            //ajouter en BDD
            $em->flush();
            $msg = "Le tatouage : ".$tattoo->getId()." a été modifié en BDD";
        }
        return $this->render('tattoo/update.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
        ]);
    }

    #[Route('/admin/tattoo/delete/{id}', name:'app_admin_tattoo_delete')]
    public function deleteTattoo(int $id, TattooRepository $repo,
    EntityManagerInterface $em){
        $tattoo = $repo->find($id);
        $em->remove($tattoo);
        $em->flush();
        return $this->redirectToRoute('app_admin_tattoo_all');
    }

}
