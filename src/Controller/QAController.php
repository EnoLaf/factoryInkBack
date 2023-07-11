<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\QA;
use App\Repository\QARepository;
use App\Form\QAType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Utils;

class QAController extends AbstractController
{
    #[Route('/qa', name: 'app_qa')]
    public function index(): Response
    {
        return $this->render('qa/index.html.twig', [
            'controller_name' => 'QAController',
        ]);
    }

    #[Route('/admin/qa/create', name: 'app_admin_qa_create')]
    public function qaCreate(EntityManagerInterface $em, Request $request):Response
    {   
        $msg = "";
        //Instancier un objet contact
        $qa = new QA();
        //instancier un objet formulaire
        $form = $this->createForm(QAType::class, $qa);
        //récupérer les données
        $form->handleRequest($request);
        //test si le formulaire est submit
        if($form->isSubmitted()){
            //nettoyage des inputs
            $question = Utils::cleanInputStatic($request->request->all('qa')['question']);
            $answer = Utils::cleanInputStatic($request->request->all('qa')['answer']);
            //set des attributs nettoyé
            $qa->setQuestion($question);
            $qa->setAnswer($answer);
            
            //persister les données
            $em->persist($qa);
            //ajoute en BDD
            $em->flush();
            $msg = "La question : ".$qa->getQuestion()." a été ajoutée en BDD";

        }
        return $this->render('qa/form.html.twig', [
            'msg'=> $msg,
            'form'=> $form->createView(),
        ]);
    }

    #[Route('/admin/qa/all', name:'app_admin_qa_all')]
    public function showAllArticle(QARepository $qaRepository):Response{
        //récuperer dans un tableau tous les articles
        $qa = $qaRepository->findAll();
        return $this->render('qa/index.html.twig', [
            'list'=> $qa,
        ]);
    }

    #[Route('/admin/qa/update/{id}', name:'app_admin_qa_update')]
    public function updateCategorie(int $id, QARepository $repo,
    EntityManagerInterface $em, Request $request,){
        $msg = "";
        //Récupérer la catégorie
        $qa = $repo->find($id);
        //créer le formulaire
        $form = $this->createForm(QAType::class, $qa);
        //Récupération des datas du formulaire
        $form->handleRequest($request);
        //tester si le formulaire est submit
        if($form->isSubmitted() AND $form->isValid()){
            //persister les données du formulaire
            $em->persist($qa);
            //ajouter en BDD
            $em->flush();
            $msg = "La question : ".$qa->getQuestion()." a été modifié en BDD";
        }
        return $this->render('qa/update.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
        ]);
    }

    #[Route('/admin/qa/delete/{id}', name:'app_admin_qa_delete')]
    public function deleteCategorie(int $id, QARepository $repo,
    EntityManagerInterface $em){
        $qa = $repo->find($id);
        $em->remove($qa);
        $em->flush();
        return $this->redirectToRoute('app_admin_qa_all');
    }
}
