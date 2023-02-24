<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TabController extends AbstractController
{
    #[Route('/tab', name: 'app_tab')]
    public function index(): Response
    {   $users=[
        ['firstname' =>'ahmed','name'=>'jd','age'=>21],
        ['firstname' =>'mohamed','name'=>'jd','age'=>21],
        ['firstname' =>'sabri','name'=>'jd','age'=>21],
        ['firstname' =>'stoura','name'=>'jaffela','age'=>41],
    ];
        return $this->render('tab/index.html.twig', [
            'users' => $users,
        ]); 
    }
}

