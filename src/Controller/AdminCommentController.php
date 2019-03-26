<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{

    /**
     * @var CommentRepository
     */
    private $repo;

    /*
     * @var ObjectManager
     */
    private $manager;

    public function __construct(CommentRepository $repo, ObjectManager $manager)
    {
        $this->repo = $repo;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/comments", name="admin.comments.index")
     */
    public function index()
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $this->repo->findAll(),
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/modifier", name="admin.comments.edit")
     * @param Comment $comment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Comment $comment, Request $request)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->manager->persist($comment);
            $this->manager->flush();

            $this->addFlash(
                'success',
                "Modifications du commentaire n°<strong>{$comment->getId()}</strong> effectué avec succès - le ".date('d/m/Y à H:i').""
            );

            return $this->redirectToRoute('admin.comments.index');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/delete", name="admin.comments.delete")
     * @param Comment $comment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Comment $comment)
    {
        $this->manager->remove($comment);
        $this->manager->flush();

        $this->addFlash(
            'success',
            "Suppression du commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> effectué avec succès - le ".date('d/m/Y à H:i').""
        );

        return $this->redirectToRoute('admin.comments.index');
    }
}
