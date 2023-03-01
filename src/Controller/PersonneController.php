<?php

namespace App\Controller;
use App\Entity\Personne ;
use App\Event\AddPersonneEvent;
use App\Event\ListeAllPersonneEvent;
use App\Form\PersonneType;
use App\service\Helpers;
use App\service\MailerService;
use App\service\PdfServices;
use App\service\UploaderService;
use Doctrine\Persistence\ManagerRegistry ;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Isbn;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcherEventDispatcherInterface;

#[
    Route('/personne'),
    IsGranted('ROLE_USER')
    
]
class PersonneController extends AbstractController
{   

    public function __construct(private LoggerInterface $logger, private Helpers $helper, EventDispatcherInterface $dispatcher)
    {}
    #[
        Route('/', name: 'personne.list'),
        IsGranted("ROLE_USER")  
    ]
    public function findAll(ManagerRegistry $doctrine): Response
    {
        $repository=$doctrine->getRepository(Personne::class);
        $personnes=$repository->findAll();
       # $listeAllPersonneEvent =new listeAllPersonneEvent(count($personnes));
       # $this->dispatcher->dispatch($listeAllPersonneEvent, ListeAllPersonneEvent::LISTE_ALL_PERSONNE_EVENT);

        return $this->render('personne/all.html.twig',['personnes'=>$personnes]);
    }
/*
    #[Route('/add', name: 'personne.add')]
    public function appPersonne(ManagerRegistry $doctrine,Request $request): Response
    {   
        $personne=new Personne();
        $form=$this->createForm(PersonneType::class,$personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');

        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $entityManager=$doctrine->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();

            $this->addFlash('success',$personne->getName()."a ete ajouter aver success");

            return $this->redirectToRoute('personne.list');
        }else{
            return $this->render('personne/add.html.twig',['form'=>$form->createView()]);

        }
    }
*/
    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfServices $pdf) {
        $html = $this->render('personne/findOne.html.twig', ['personne' => $personne]);
        $pdf->showPdfFile($html);
    }


    #[Route('/edit/{id?0<\d+>}', name: 'personne.edit')]
    public function editPersonne(ManagerRegistry $doctrine, Request $request, Personne $personne=null, UploaderService $uploaderService, MailerService $mailer): Response
    {   $this->denyAccessUnlessGranted('ROLE_ADMIN');
         $new=false ;
        if(!$personne)
        {   $new=false;
            $personne=new Personne();
        }
        $form=$this->createForm(PersonneType::class,$personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {   
             $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $directory=$this->getParameter('personnes_directory');
                $personne->setImage($uploaderService->uploadFile($photo, $directory));
            }
            if($new)
            {
                $message="a été ajouter avec success";
                $personne->setCreatedBy($this->getUser());
            } else {
            $message="a ete updated avec success";
            }

            $Manager=$doctrine->getManager();
            $Manager->persist($personne);
            
            $Manager->flush();

            if($new){
                $addPersonneEvent=new AddPersonneEvent($personne);
                $this->dispatcher->dispatch($addPersonneEvent, AddPersonneEvent::ADD_PERSONNE_EVENT);
            }
           
            $mailMessage=$personne->getName()."  ".$personne->getFirstname().'   '.$message ;

            $this->addFlash('success',$personne->getName().$message);

            $mailer->sendEmail(subject:$mailMessage);

            return $this->redirectToRoute('personne.list');
        }else{
            return $this->render('personne/add.html.twig',['form'=>$form->createView()]);

        }
    }
    #[Route('/{id<\d+>}', name: 'personne.one')]
    public function findOne(ManagerRegistry $doctrine,$id): Response
    {
        $repository=$doctrine->getRepository(Personne::class);
        $personne=$repository->findOneBy(['id'=>$id]);
       // $personne=$repository->find($id);
       if(!$personne)
       { $this->addFlash('error',"la personne d'id $id n'éxiste pas");
        return $this->redirectToRoute('personne.list');
    }
        return $this->render('personne/findOne.html.twig',['personne'=>$personne]);
    }

    #[Route('/page/{page?1}/{nbr?10}', name: 'personne.page')]
    public function findbypage(ManagerRegistry $doctrine,$nbr,$page): Response
    {
        echo ($this->helper->sayCc());
        $repository=$doctrine->getRepository(Personne::class);
        
        $personnes=$repository->findBy([],['age'=>'ASC'],$nbr,($page-1)*$nbr);
        $nbrper=count($repository->findAll());
        $nbrPage=ceil($nbrper/$nbr) ;
        return $this->render('personne/index.html.twig',['personnes' => $personnes, 
                                                          'isPaginated' => true ,
                                                            'nbrPage'=>$nbrPage,
                                                            'nbr'=>$nbr,
                                                             'page'=>$page
                                                        ]);
    } 
    #[
        Route('/delete/{id<\d+>}', name: 'personne.delete'),
        IsGranted('ROLE_ADMIN')
    ]
 public function delete(ManagerRegistry $doctrine ,Personne $personne=null):RedirectResponse
 {
    if($personne)
    {
        $manager=$doctrine->getManager();
        $manager->remove($personne);
        $manager->flush();
        $this->addFlash('success','la personne a été supprimer avec succés');
    }else{
        $this->addFlash('error',"la personne n'exixte pas");
    }
    return $this->redirectToRoute('personne.list');
 }
 #[Route('/update/{id<\d+>}', name: 'personne.update')]
 public function update(ManagerRegistry $doctrine ,Personne $personne=null):RedirectResponse
 {
    if($personne)
    {
        $personne->setAge($personne->getAge() +1);

        $manager=$doctrine->getManager();
        $manager->persist($personne);

        $manager->flush();
        $this->addFlash('success','la personne a été modifiée avec succés');
    }else{
        $this->addFlash('error',"la personne n'exixte pas");
    }
    return $this->redirectToRoute('personne.list');
 }

 #[Route('/all/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
 public function personnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {

   
     $repository = $doctrine->getRepository(Personne::class);
     $personnes = $repository->findPersonneByAgeInterval($ageMin,$ageMax) ;
     
     return $this->render('personne/all.html.twig', ['personnes' => $personnes]);
 }

 #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.stats')]
 public function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {
     $repository = $doctrine->getRepository(Personne::class);
     $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
     return $this->render('personne/stats.html.twig', [
         'stats' => $stats[0],
         'ageMin'=> $ageMin,
         'ageMax' => $ageMax]
     );
 }
}
