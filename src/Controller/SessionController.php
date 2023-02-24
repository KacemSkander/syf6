<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(Request $request): Response
    {   
        
        $session=$request->getSession();
        if($session->has('nb'))
        {
            $nbr=$session->get('nb')+1 ;
           
        }else{
            $nbr=1 ;
        }
        $session->set('nb',$nbr);
        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
        ]);
    }
}
