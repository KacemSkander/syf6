<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListeAllPersonneEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener
{
    public function __construct(
        private LoggerInterface $logger) {

     }
   public function onPersonneAdd(AddPersonneEvent $event){
    $this->logger->debug("cc je suis entrain d'ecouter et la personne ajoutée est ".$event->getPersonne()->getName());
   }
   public function onListeAllPersonnes(ListeAllPersonneEvent $event){
    $this->logger->debug("le nb de personne dans la base est ".$event->getNbPersonne());
   }

   public function onListeAllPersonnes2(ListeAllPersonneEvent $event){
    $this->logger->debug("le 2eme listener ".$event->getNbPersonne());
   }

   public function LogKernelRequest(KernelEvent $event){
    dd($event->getRequest());
   }

}
