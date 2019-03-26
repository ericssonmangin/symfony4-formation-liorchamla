<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @var AdRepository
     */
    private $repo;

    /*
     * @var ObjectManager
     */
    private $manager;

    public function __construct(AdRepository $repo, ObjectManager $manager)
    {
        $this->repo = $repo;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/ads", name="admin.ads.index")
     */
    public function index()
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $this->repo->findAll(),
        ]);
    }

    /**
     * @Route("/admin/ads/{id}/modifier", name="admin.ads.edit")
     * @param Ad $ad
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Ad $ad, Request $request)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->manager->persist($ad);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Modifications de l'annonce (<strong>{$ad->getTitle()}</strong>) effectuées avec succès - le ".date('d/m/Y à H:i').""
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/ads/{id}/delete", name="admin.ads.delete")
     * @param Ad $ad
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Ad $ad)
    {
        if(count($ad->getBookings()) > 0){
            $this->addFlash(
                'danger',
                "Impossible de supprimer cette annonce, car elle contient déjà des réservations"
            );
        }else{
            $this->manager->remove($ad);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Suppression de l'annonce effectué avec succès - le ".date('d/m/Y à H:i').""
            );
        }

        return $this->redirectToRoute('admin.ads.index');
    }
}
