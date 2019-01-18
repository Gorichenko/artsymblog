<?php

namespace App\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Comment;
use App\Form\Blog\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Comment controller.
 */
class CommentController extends Controller
{
    protected $session;
    protected $commentType;

    public function __construct(SessionInterface $session, CommentType $commentType)
    {
        $this->session = $session;
        $this->commentType = $commentType;
    }

    /**
     * @Route("/comment/new/{blog_id}"),
     * methods={"GET"},
     * name="app_blog_comment_new",
     * requirements={"blog_id"="\d+"}
     */
    public function new($blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $comment = new Comment();
        $comment->setBlog($blog);
        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('blog/comment/form.html.twig', array(
            'comment' => $comment,
            'form'   => $form->createView()
        ));
    }

    /**
     * @Route("/comment/{blog_id}"),
     * methods={"POST"},
     * name="app_blog_comment_create",
     * requirements={"blog_id"="\d+"}
     */
    public function create(Request $request, $blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $comment  = new Comment();
        $comment->setBlog($blog);
        $form    = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($this->session->get('user')) {
            $comment->setUser($this->session->get('user')['name']);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            /*
             * Реализация добавления комментариев через очереди
             * для активации php bin/console app:getcomment
             *
             * */
            $request->request->get($this->commentType->getBlockPrefix());
            $queue_comment = [];

            if (isset($request->request->get($this->commentType->getBlockPrefix())['user'])) {
                $queue_comment['user'] = $request->request->get($this->commentType->getBlockPrefix())['user'];
            } else {
                $queue_comment['user'] = $this->session->get('user')['name'];
            }
            $queue_comment['comment'] = $request->request->get($this->commentType->getBlockPrefix())['comment'];
            $queue_comment['blog_id'] = $blog_id;

            $connection = new AMQPStreamConnection("rabbit1", 5672, 'rabbitmq', 'rabbitmq');
            $channel = $connection->channel();
            $channel->queue_declare('comment_queue', false,false, false, false);
            $msg = new AMQPMessage(serialize($queue_comment));
            $channel->basic_publish($msg, '', 'comment_queue');

            return $this->redirect($this->generateUrl('app_blog_blog_show', array(
                    'id' => $comment->getBlog()->getId())) .
                '#comment-' . $comment->getId()
            );
        }

        return $this->render('blog/comment/create.html.twig', array(
            'comment' => $comment,
            'form'    => $form->createView()
        ));
    }

    protected function getBlog($blog_id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $blog = $em->getRepository('App\Entity\Blog')->find($blog_id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $blog;
    }

}

