<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
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
     * @Route("/nos-annonces", name="ad.index")
     */
    public function index()
    {
        $ads = $this->repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * @Route("/annonce/creer", name="ad.create")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getPictures() as $pic){
                $pic->setAd($ad);
                $this->manager->persist($pic);
            }

            $ad->setAuthor($this->getUser());

            $this->manager->persist($ad);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Nouvelle annonce (<strong>{$ad->getTitle()}</strong>) enregistrée avec succès - le ".date('d/m/Y à H:i').""
            );

            return $this->redirectToRoute('ad.show', ['slug' => $ad->getSlug()]);
        }

        return $this->render('ad/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/annonce/{slug}/modifier", name="ad.edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()")
     * @param Ad $ad
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Ad $ad, Request $request)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getPictures() as $pic){
                $pic->setAd($ad);
                $this->manager->persist($pic);
            }
            $this->manager->persist($ad);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Modifications de l'annonce (<strong>{$ad->getTitle()}</strong>) effectuées avec succès - le ".date('d/m/Y à H:i').""
            );

            return $this->redirectToRoute('ad.show', ['slug' => $ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * @Route("/annonce/{slug}/supprimer", name="ad.delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()")
     * @param Ad $ad
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Ad $ad)
    {
        $this->manager->remove($ad);
        $this->manager->flush();

        $this->addFlash(
            'success',
            "Suppression de l'annonce (<strong>{$ad->getTitle()}</strong>) effectuée avec succès - le ".date('d/m/Y à H:i').""
        );

        return $this->redirectToRoute('ad.index');
    }


    /**
     * @Route("/annonce/{slug}", name="ad.show")
     * @param Ad $ad
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

}
