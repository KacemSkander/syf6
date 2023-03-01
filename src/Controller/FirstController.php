<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
   
    public function index(  $name,$firstname): Response
    {
        return $this->render('first/index.html.twig', [
            'name' =>$name,
            'firstname' =>$firstname
        ]);
    }
    #[Route('/first', name: 'app_first')]
    public function index1(  ): Response
    {
        return $this->render('first/first.html.twig', [
            'name' =>'kacem',
            'firstname' =>'Skander'
        ]);
    }
    #[Route('/template', name: 'template')]
    public function template( ): Response
    {
        return $this->render('template.html.twig');
    }
}
