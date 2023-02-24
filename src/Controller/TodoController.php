<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo')]
class TodoController extends AbstractController
{
    #[Route('/delete/{name}','todo.delete')]
    public function deleteTodo(Request $request,$name) :RedirectResponse
    {
        $session=$request->getSession();
       
        if($session-> has('todo'))
        {   $todo=$session->get('todo');
            if(!isset($todo[$name])){
                $this->addFlash('error',"le todo d'id $name n'existe pas ");
            }else{
                unset($todo[$name]);
                $this->addFlash('success',"le todo d'id $name a été supprimé avec succé");
                $session->set('todo',$todo);
            }
        }else{
            $this->addFlash('error',"la liste de todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_todo');
    } 

    #[Route('/', name: 'app_todo')]
    public function index(Request $request): Response
    {
        $session=$request->getSession();

        if(!$session-> has('todo'))
        {
            $todo=[
                'achat'=> 'acheter clé usb' ,
                'cours'=> 'finaliser mon cours' ,
                'correction' => 'corriger mes examens'
            ];

            $session->set('todo',$todo);
            $this->addFlash('info',"la liste de todos viens d'etre initialisée");
        }
        return $this->render('todo/index.html.twig');
    }
    #[Route('/add/{name}/{content}','todo.add',defaults:['content'=>'sf6'])]
    public function addTodo(Request $request,$name,$content):RedirectResponse
    {
        $session=$request->getSession();
       
        if($session-> has('todo'))
        {   $todo=$session->get('todo');
            if(isset($todo[$name])){
                $this->addFlash('error',"le todo d'id $name existe déja ");
            }else{
                $todo[$name]=$content ;
                $this->addFlash('success',"le todo d'id $name a été ajouté avec succé");
                $session->set('todo',$todo);
            }
        }else{
            $this->addFlash('error',"la liste de todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_todo');
    }

    #[Route('/update/{name}/{content?none}','todo.update')]
    public function updateTodo(Request $request,$name,$content):RedirectResponse
    {
        $session=$request->getSession();
       
        if($session-> has('todo'))
        {   $todo=$session->get('todo');
            if(!isset($todo[$name])){
                $this->addFlash('error',"le todo d'id $name n'existe pas ");
            }else{
                $todo[$name]=$content ;
                $this->addFlash('success',"le todo d'id $name a été mis a jour avec succé");
                $session->set('todo',$todo);
            }
        }else{
            $this->addFlash('error',"la liste de todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_todo');
    }

    

    #[Route('/reset','todo.reset')]
    public function resetTodo(Request $request) :RedirectResponse
    {
        $session=$request->getSession();
        //$session->remove('todo');
        if($session-> has('todo'))
        {   
            $todo=[
                'achat'=> 'acheter clé usb' ,
                'cours'=> 'finaliser mon cours' ,
                'correction' => 'corriger mes examens'
            ];

            $session->set('todo',$todo);
            $this->addFlash('info',"la liste de todos viens d'etre reinitialisée");
        }
        return $this->redirectToRoute('app_todo');
    }
}
