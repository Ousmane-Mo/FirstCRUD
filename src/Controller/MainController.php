<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\CrformType;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/main", name="app_main")
     */
    public function index(ArticlesRepository $repo): Response
    {   
        $datas = $repo->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'datas'=> $datas
        ]);
    }
    /**
     * @Route("/create", name="app_create", methods={"GET","POST"})
     */
    public function create(Request $request, ArticlesRepository $repo): Response
    {   
        $articles = new Articles(); 
        $form = $this->createForm(CrformType::class, $articles);
        $form->handleRequest($request);

        // On enregistre l'article dans la BDD
        if ($form->isSubmitted() && $form->isValid()) {
            
            // $send = $this->getDoctrine()->getManager();
            // $send->persist($articles);
            // $send->flush();
            $repo->add($articles,true); //this line replace the three commented line above
            $this->addFlash('success','Les données ont bien été envoyés');
            return $this->redirectToRoute("app_main");
        }  
        
        return $this->render('main/CrForm.html.twig', [
            'controller_name' => 'MainController',
            'Crform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request, ArticlesRepository $repo): Response
    { 
        $data = $repo -> findOneBy(['id' => $id]);
        $form = $this->createForm(CrformType::class, $data);
        $form->handleRequest($request);
        
        // On enregistre l'article dans la BDD
        if ($form->isSubmitted() && $form->isValid()) {
            
            $repo->add($data,true);
            $this->addFlash('success','Les données ont bien été modifiées');
            return $this->redirectToRoute("app_main");
        }  
        return $this->render('main/EditData.html.twig', [
            'controller_name' => 'MainController',
            'Edform'=> $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_delete", methods={"GET","POST"})
     */
    public function delete($id, ArticlesRepository $repo): Response
    { 
        $data = $repo -> find($id);
        $repo->remove($data,true);
        $this->addFlash('success','L\'article a bien été supprimé');
        return $this->redirectToRoute('app_main');
    }
}

