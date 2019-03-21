<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserPageController extends AbstractController
{
    /**
     * @Route("/utilisateur/{slug}", name="user.show")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(User $user)
    {
        return $this->render('user_page/show.html.twig', [
            'user' => $user
        ]);
    }
}
