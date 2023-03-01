<?php

namespace App\Event;

use App\Entity\Personne;
use Symfony\Contracts\EventDispatcher\Event;

class ListeAllPersonneEvent extends Event
{
    const LISTE_ALL_PERSONNE_EVENT = 'personne.liste_alls';

    public function __construct(private int $nbPersonne) {}

    public function getNbPersonne(): int {
        return $this ->nbPersonne;
    }

}
