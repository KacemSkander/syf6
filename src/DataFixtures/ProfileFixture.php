<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {   

        $profile=new Profile();
        $profile->setRs('facebook');
        $profile->setUrl('https://www.facebook.com/ahmedbenkhlifa.jaidi');
        

        $profile2=new Profile();
        $profile2->setRs('linkedin');
        $profile2->setUrl('https://www.linkedin.com/in/ahmed-jaidi-65357a1ba/');
        

        $profile3=new Profile();
        $profile3->setRs('instagram');
        $profile3->setUrl('https://www.facebook.com/ahmedbenkhlifa.jaidi');
      

        $profile4=new Profile();
        $profile4->setRs('github');
        $profile4->setUrl('https://github.com/ahmedjd499');
       
        $manager->persist($profile);
        $manager->persist($profile2);
        $manager->persist($profile3);
        $manager->persist($profile4);
        $manager->flush();
    }
}
