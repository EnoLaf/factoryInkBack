<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Artist;
use App\Repository\ArtistRepository;
use App\Form\ArtistType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Utils;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArtistController extends AbstractController
{
    #[Route('/artist', name: 'app_artist')]
    public function index(): Response
    {
        return $this->render('artist/index.html.twig', [
            'controller_name' => 'ArtistController',
        ]);
    }

    #[Route('/admin/artist/create', name: 'app_admin_artist_create')]
    public function artistCreate(EntityManagerInterface $em, Request $request, 
    SluggerInterface $slugger):Response
    {   
        $msg = "";
        //Instancier un objet contact
        $artist = new Artist();
        //instancier un objet formulaire
        $form = $this->createForm(ArtistType::class, $artist);
        //récupérer les données
        $form->handleRequest($request);
        //test si le formulaire est submit
        if($form->isSubmitted()){
            //nettoyage des inputs
            $name = Utils::cleanInputStatic($request->request->all('artist')['name']);
            $style = Utils::cleanInputStatic($request->request->all('artist')['style']);
            $resume = Utils::cleanInputStatic($request->request->all('artist')['resume']);
            //set des attributs nettoyé
            $artist->setName($name);
            $artist->setStyle($style);
            $artist->setResume($resume);
            $artist->setLink('/artistes-'.$artist->getName());

            $file = $form->get('profilePicture')->getData();
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
                        $this->getParameter('artist_uploads'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $artist->setProfilePicture($newFilename);
            }
            
            //persister les données
            $em->persist($artist);
            //ajoute en BDD
            $em->flush();
            $msg = "L'artiste : ".$artist->getName()." a été ajouté.e en BDD";

        }
        return $this->render('artist/form.html.twig', [
            'msg'=> $msg,
            'form'=> $form->createView(),
        ]);
    }

    #[Route('/admin/artist/all', name:'app_admin_artist_all')]
    public function showAllArtists(ArtistRepository $artistRepository):Response{
        //récuperer dans un tableau tous les articles
        $artists = $artistRepository->findAll();
        return $this->render('artist/index.html.twig', [
            'list'=> $artists,
        ]);
    }

    #[Route('/api/artists/all', name:'app_api_artists_all', methods:'GET')]
    public function showAllArtistsApi(ArtistRepository $artistRepository):Response{
        return $this->json($artistRepository->findAll(),200, [], ['groups' => 'artists:readAll']);
    }
    #[Route('/api/artists/{id}', name:'app_api_artists_id', methods:'GET')]
    public function showOneArtistsApi(ArtistRepository $artistRepository, int $id):Response{
        return $this->json($artistRepository->find($id),200, [], ['groups' => 'artists:readAll']);
    }

    #[Route('/admin/artist/update/{id}', name:'app_admin_artist_update')]
    public function updateArtist(int $id, ArtistRepository $repo,
    EntityManagerInterface $em, Request $request,){
        $msg = "";
        //Récupérer la catégorie
        $artist = $repo->find($id);
        //créer le formulaire
        $form = $this->createForm(ArtistType::class, $artist);
        //Récupération des datas du formulaire
        $form->handleRequest($request);
        //tester si le formulaire est submit
        if($form->isSubmitted() AND $form->isValid()){
            //persister les données du formulaire
            $em->persist($artist);
            //ajouter en BDD
            $em->flush();
            $msg = "L'artiste : ".$artist->getName()." a été modifié.e en BDD";
        }
        return $this->render('artist/update.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
        ]);
    }

    #[Route('/admin/artist/delete/{id}', name:'app_admin_artist_delete')]
    public function deleteArtist(int $id, ArtistRepository $repo,
    EntityManagerInterface $em){
        $artist = $repo->find($id);
        $em->remove($artist);
        $em->flush();
        return $this->redirectToRoute('app_admin_artist_all');
    }

}
