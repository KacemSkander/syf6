<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {   
        $data=['job1','job2','job3','job4','job5','job6','job7','job8','job9','job10','job11'];
        for($i=0;$i<count($data);$i++)
        {
            $job = new Job();
            $job->setDesignation($data[$i]);
            $manager->persist($job);
        }
        

        $manager->flush();
    }
}
