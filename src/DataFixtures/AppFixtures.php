<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ini_set('memory_limit', '4096M');  // Augmente la limite de mémoire à 1024 Mo

        $loader = new NativeLoader();
        $objectSet = $loader->loadFile(__DIR__.'/starter.yaml')->getObjects();
        foreach($objectSet as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }
}
