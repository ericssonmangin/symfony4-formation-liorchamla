<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\PasswordUpdateType;
use App\Form\ProfileType;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
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
     * @Route("/mon-compte/inscription", name="user.register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Votre compte à bien été crée - le ".date('d/m/Y à H:i')." <br> Vous pouvez désormais vous connecter avec vos identifiants"
            );

            return $this->redirectToRoute('user.login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/editer-profil", name="user.profile")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function profile(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Vos données ont bien été modifiées"
            );

            return $this->redirectToRoute('user.login');
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/modifier-mot-de-passe", name="user.password")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $updatePassword = new PasswordUpdate();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $updatePassword);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $newPassword = $updatePassword->getNewPassword();
            $hash = $encoder->encodePassword($user, $newPassword);
            $user->setHash($hash);

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Votre mot de passe a bien été modifié"
            );

            return $this->redirectToRoute('user.login');
        }

        return $this->render('user/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/identification", name="user.login")
     * @param AuthenticationUtils $utils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $authError = $utils->getLastAuthenticationError();

        return $this->render('user/login.html.twig', [
            'authError' => $authError !== null
        ]);
    }

    /**
     * @Route("/mon-compte/deconnexion", name="user.logout")
     */
    public function logout(){}

    /**
     * @Route("/mon-compte", name="user.account")
     * @IsGranted("ROLE_USER")
     */
    public function account()
    {
        return $this->render('user_page/show.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/mon-compte/reservations", name="user.bookings")
     * @IsGranted("ROLE_USER")
     */
    public function bookings()
    {
        return $this->render('user/bookings.html.twig');
    }

}
