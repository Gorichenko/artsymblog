<?php

namespace App\Controller\Blog;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Blog\Enquiry;
use App\Form\Blog\EnquiryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\QueueService;

class PageController extends Controller
{
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/homepage"),
     * methods={"GET"},
     * name="app_blog_page_index"
     */
    public function index()
    {
        $user = $this->session->get('user');

        $em = $this->getDoctrine()
            ->getManager();

        $blogs = $em->getRepository('App\Entity\Blog')
            ->getLatestBlogs();

        return $this->render('blog/index.html.twig', array(
            'blogs' => $blogs,
            'user' => $user ? $user : null
        ));
    }

    /**
     * @Route("/about"),
     * methods={"GET"},
     * name="app_blog_page_about"
     */
    public function about()
    {
        return $this->render('blog/about.html.twig');
    }

    /**
     * @Route("/contact"),
     * methods="GET|POST",
     * name="app_blog_page_contact"
     */
    public function contact(Request $request, \Swift_Mailer $mailer)
    {
        $enquiry = new Enquiry();

        $form = $this->createForm(EnquiryType::class, $enquiry);

        if ($request->isMethod($request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $message = (new \Swift_Message('mail message'))
                    ->setSubject('Contact enquiry from symblog')
                    ->setFrom('symblog@gmail.com')
                    ->setTo('gorichenko1990@gmail.com')
                    ->setBody($this->renderView('blog/email_template/contactEmail.txt.twig', array('enquiry' => $enquiry)));

                $mailer->send($message);

                $this->get('session')->getFlashBag()->add('blogger-notice', 'Your contact enquiry was successfully sent. Thank you!');

                return $this->redirect($this->generateUrl('app_blog_page_contact'));
            }
        }

        return $this->render('blog/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function sidebar()
    {
        $em = $this->getDoctrine()
            ->getManager();

        $tags = $em->getRepository('App\Entity\Blog')
            ->getTags();

        $tagWeights = $em->getRepository('App\Entity\Blog')
            ->getTagWeights($tags);

        $commentLimit = $this->container
            ->getParameter('app_blog.comments.latest_comment_limit');
        $latestComments = $em->getRepository('App\Entity\Comment')
            ->getLatestComments($commentLimit);

        return $this->render('blog/sidebar.html.twig', array(
            'latestComments'    => $latestComments,
            'tags'              => $tagWeights
        ));
    }
}
