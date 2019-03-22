<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @var BookingRepository
     */
    private $repo;

    /*
     * @var ObjectManager
     */
    private $manager;

    public function __construct(BookingRepository $repo, ObjectManager $manager)
    {
        $this->repo = $repo;
        $this->manager = $manager;
    }

    /**
     * @Route("/annonce/{slug}/book", name="booking.create")
     * @IsGranted("ROLE_USER")
     * @param Ad $ad
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function book(Ad $ad, Request $request)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);

            if(!$booking->isBookableDates()){
                $this->addFlash(
                    'warning',
                    "Impossible de réserver pour ces dates prévues car une réservation est déjà en cours."
                );
            }else{
                $this->manager->persist($booking);
                $this->manager->flush();

                $this->addFlash(
                    'success',
                    "Félicitations ! Votre réservation n° {$booking->getId()} pour l'hébergement <strong>{$ad->getTitle()}</strong> a bien été prise en compte, le ".date('d/m/Y à H:i').""
                );

                return $this->redirectToRoute('booking.show', ['id' => $booking->getId()]);
            }

        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reservation/{id}", name="booking.show")
     * @IsGranted("ROLE_USER")
     * @param Booking $booking
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Booking $booking)
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }
}
