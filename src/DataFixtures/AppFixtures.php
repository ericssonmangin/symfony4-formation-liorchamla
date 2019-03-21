<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Fixtures Ad
        for($i = 1; $i <= 30; $i++){
            $ad = new Ad;

            $ad->setTitle($faker->sentence(4, true))
                ->setPrice(mt_rand(40, 200))
                ->setIntro($faker->paragraph(2))
                ->setContent($faker->paragraphs(5, true))
                ->setImage($faker->imageUrl(1000, 400))
                ->setRooms(mt_rand(1, 5));

            // Fixtures Pictures (2 - 5 per Ad)
            for($j = 1; $j <= mt_rand(2, 5); $j++){
                $picture = new Picture();

                $picture->setAd($ad)
                        ->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence(4, true));

                $manager->persist($picture);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
