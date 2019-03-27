<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\PaginationService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
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
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin.bookings.index")
     * @param int $page
     * @param PaginationService $pagination
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Booking::class)
                    ->setCurrentPage($page)
                    ->setLimit(15);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/modifier", name="admin.bookings.edit")
     * @param Booking $booking
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Booking $booking, Request $request)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $booking->setAmount(0);

            $this->manager->persist($booking);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Modifications de la réservation n°<strong>{$booking->getId()}</strong> effectués avec succès - le ".date('d/m/Y à H:i').""
            );

            return $this->redirectToRoute('admin.bookings.index');
        }

        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/delete", name="admin.bookings.delete")
     * @param Booking $booking
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Booking $booking)
    {

        $this->manager->remove($booking);
        $this->manager->flush();

        $this->addFlash(
            'success',
            "Suppression de la réservation de <strong>{$booking->getBooker()->getFullName()}</strong> effectuée avec succès - le ".date('d/m/Y à H:i').""
        );

        return $this->redirectToRoute('admin.bookings.index');

    }

}
