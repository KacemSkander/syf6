<?php

namespace App\DataFixtures;

use App\Entity\Hobi;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobiFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data=['hob1','hob2','hob3','hob4','hob5','hob6','hob7','hob8','hob9','hob10','hob11'];
        for($i=0;$i<count($data);$i++)
        {
            $hobby = new Hobi();
            $hobby->setDesignation($data[$i]);
            $manager->persist($hobby);
        }

        $manager->flush();
    }
}
