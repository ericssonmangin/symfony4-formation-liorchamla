<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Fixtures Role Admin + User Admin
        $roleAdmin = new Role();
        $roleAdmin->setTitle('ROLE_ADMIN');
        $manager->persist($roleAdmin);

        $userAdmin = new User();
        $userAdmin->setFirstName('Eric')
                ->setLastName('Mangin')
                ->setEmail("mangin.ericsson@gmail.com")
                ->setHash($this->encoder->encodePassword($userAdmin, 'admin'))
                ->setAvatar("https://avatars.io/twitter/goku")
                ->setPresentation($faker->paragraph(2))
                ->setDescription($faker->paragraphs(5, true))
                ->addUserRole($roleAdmin);
        $manager->persist($userAdmin);

        // Fixtures User
        $users = [];
        $genders = ['male', 'female'];

        for($i = 1; $i <= 10; $i++){
            $user = new User();

            $gender = $faker->randomElement($genders);
            $avatar = 'https://randomuser.me/api/portraits/';
            $avatarId = $faker->numberBetween(1, 99) . '.jpg';
            $avatar .= ($gender == 'male' ? 'men/' : 'women/') . $avatarId;

            $user->setFirstName($faker->firstname($gender))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setHash($this->encoder->encodePassword($user, 'admin'))
                ->setAvatar($avatar)
                ->setPresentation($faker->paragraph(2))
                ->setDescription($faker->paragraphs(5, true));

            $manager->persist($user);
            $users[] = $user;
        }

        // Fixtures Ad
        for($i = 1; $i <= 30; $i++){
            $ad = new Ad();

            $ad->setTitle($faker->sentence(4, true))
                ->setPrice(mt_rand(40, 200))
                ->setIntro($faker->paragraph(2))
                ->setContent($faker->paragraphs(5, true))
                ->setImage($faker->imageUrl(1000, 400))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($users[mt_rand(0, count($users) - 1)]);

            // Fixtures Pictures (2 - 5 per Ad)
            for($j = 1; $j <= mt_rand(2, 5); $j++){
                $picture = new Picture();

                $picture->setAd($ad)
                        ->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence(4, true));

                $manager->persist($picture);
            }

            // Fixtures Booking (0 - 5 per Ad)
            for($j = 1; $j <= mt_rand(0, 5); $j++){
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween($createdAt);
                $duration = mt_rand(1, 10);
                $endDate = (clone $startDate)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) - 1)];

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($faker->paragraph());

                $manager->persist($booking);

                // Fixtures Comment
                if(mt_rand(0, 1)){
                    $comment = new Comment();

                    $comment->setAd($ad)
                            ->setAuthor($booker)
                            ->setRating(mt_rand(1, 5))
                            ->setContent($faker->paragraph());

                    $manager->persist($comment);

                }

            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
