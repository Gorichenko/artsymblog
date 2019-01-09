<?php

namespace App\Controller\Blog;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BlogController extends Controller
{
    /**
     * @Route("/show/{id}"),
     * methods={"GET"},
     * name="app_blog_blog_show",
     * requirements={"id"="\d+"}
     */
    public function show($id)
    {
        $em = $this->getDoctrine()->getManager();

        $blog = $em->getRepository('App\Entity\Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $comments = $em->getRepository('App\Entity\Comment')
            ->getCommentsForBlog($blog->getId());

        return $this->render('blog/show.html.twig', array(
            'blog'      => $blog,
            'comments' => $comments
        ));
    }
}