<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminUserController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $repo;

    /*
     * @var ObjectManager
     */
    private $manager;

    public function __construct(UserRepository $repo, ObjectManager $manager)
    {
        $this->repo = $repo;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/users", name="admin.user.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $this->repo->findAll(),
        ]);
    }

    /**
     * @Route("/admin/users/{id}/modifier", name="admin.user.edit")
     * @param User $users
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(User $users, Request $request)
    {
        $form = $this->createForm(ProfileType::class, $users);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->manager->persist($users);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Modifications de l'utilisateur <strong>{$users->getFullName()}</strong> effectués avec succès - le ".date('d/m/Y à H:i').""
            );

            return $this->redirectToRoute('admin.user.index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $users,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/{id}/delete", name="admin.user.delete")
     * @param User $users
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(User $users)
    {
        if(count($users->getBookings()) > 0 || count($users->getAds()) > 0){
            $this->addFlash(
                'danger',
                "Impossible de supprimer cet utilisateur, car il possède déjà des annonces ou à effectué des réservations"
            );
        }else{
            $this->manager->remove($users);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Suppression de l'utilisateur <strong>{$users->getFullName()}</strong> effectué avec succès - le ".date('d/m/Y à H:i').""
            );
        }

        return $this->redirectToRoute('admin.user.index');
    }

    /**
     * @Route("/admin/login", name="admin.user.login")
     * @param AuthenticationUtils $utils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $authError = $utils->getLastAuthenticationError();

        return $this->render('admin/user/login.html.twig', [
            'authError' => $authError !== null
        ]);
    }

    /**
     * @Route("/admin/logout", name="admin.user.logout")
     */
    public function logout(){}
}
