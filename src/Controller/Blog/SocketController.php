<?php

namespace App\Controller\Blog;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\ChatUsers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SocketController extends Controller
{
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/socket"),
     * methods={"GET"},
     * name="app_blog_socket_chat",
     */
    public function chat()
    {
        if (!$this->session->get('user')) {
            return $this->redirectToRoute('app_blog_page_index');
        }

        $em = $this->getDoctrine()->getManager();
        $online_users = $em->getRepository('App\Entity\User')->getOnlineUsers($this->session->get('user')['id']);

        foreach ($online_users as &$item) {
           $result = $em->getRepository('App\Entity\ChatUsers')->isChatExists($this->session->get('user')['id'], $item['id']);
            $item['chat_id'] = $result[0]['id'];
        }

        return $this->render('blog/socket/index.html.twig', array(
            'online_users' => $online_users
        ));
    }

    /**
     * @Route("/socket/private/{user_id}"),
     * methods={"GET"},
     * name="app_blog_socket_private",
     */
    public function private($user_id)
    {
        if (!$this->session->get('user')) {
            return $this->redirectToRoute('app_blog_page_index');
        }

        $em = $this->getDoctrine()->getManager();
        $online_users = $em->getRepository('App\Entity\User')->getOnlineUsers($this->session->get('user')['id']);

        foreach ($online_users as &$item) {
            $result = $em->getRepository('App\Entity\ChatUsers')->isChatExists($this->session->get('user')['id'], $item['id']);
            $item['chat_id'] = $result[0]['id'];
        }

        $interlocutor = $em->getRepository('App\Entity\User')->getUserById($user_id)[0];
        $chat_id = $em->getRepository('App\Entity\ChatUsers')->isChatExists($this->session->get('user')['id'], $user_id);

        if (!$chat_id) {
            $chatUsers = new ChatUsers();
            $chatUsers->setUserFrom($this->session->get('user')['id']);
            $chatUsers->setUserTo($user_id);
            $em->persist($chatUsers);
            $em->flush();
        }

        $chat_id = $em->getRepository('App\Entity\ChatUsers')->isChatExists($this->session->get('user')['id'], $user_id);

        return $this->render('blog/socket/private.html.twig', array(
            'online_users' => $online_users,
            'interlocutor' => $interlocutor,
            'chat_id' => $chat_id[0]['id']
        ));
    }

    /**
     * @Route("/socket/counter"),
     * methods={"GET|POST"},
     * name="app_blog_socket_counter",
     */
    public function counter(Request $request)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $message_count = [];

        if ($request->isXmlHttpRequest() && $request->getMethod() == 'GET') {

            if ($request->query->get('user_id')) {
                $message_count = $em->getRepository('App\Entity\PrivateChat')
                    ->getMessagesCount($request->get('user_id'));
            }

            if ($request->query->get('chat_id') && $request->query->get('user_id')) {
                $message_count = $em->getRepository('App\Entity\PrivateChat')
                    ->getChatMessagesCount($request->query->get('user_id'), $request->get('chat_id'))[0];
            }
        }

        return new JsonResponse($message_count);
    }

}
