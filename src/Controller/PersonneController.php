<?php

namespace App\Controller;
use App\Entity\Personne ;
use App\Form\PersonneType;
use Doctrine\Persistence\ManagerRegistry ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/personne')]
class PersonneController extends AbstractController
{   

    #[Route('/', name: 'personne.list')]
    public function findAll(ManagerRegistry $doctrine): Response
    {
        $repository=$doctrine->getRepository(Personne::class);
        $personnes=$repository->findAll();

        return $this->render('personne/all.html.twig',['personnes'=>$personnes]);
    }

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


    #[Route('/edit/{id?0<\d+>}', name: 'personne.edit')]
    public function editPersonne(ManagerRegistry $doctrine,Request $request,Personne $personne=null,SluggerInterface $slugger): Response
    {   $new=false ;
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
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('personnes_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $personne->setImage($newFilename);
            }

            $entityManager=$doctrine->getManager();
            $entityManager->persist($personne);
            
            $entityManager->flush();
            if($new)
            {
                $message="a ete ajouter avec success";
            }else
            $message="a ete updated avec success";

            $this->addFlash('success',$personne->getName().$message);

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
    #[Route('/delete/{id<\d+>}', name: 'personne.delete')]
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
