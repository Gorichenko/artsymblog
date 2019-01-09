<?php

namespace App\Controller\Blog;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use App\Form\Blog\LoginType;
use App\Form\Blog\SingUpType;

class LoginController extends Controller
{
    protected $session;
    protected $user = null;

    public function __construct(
        SessionInterface $session
    )
    {
        $this->session = $session;
    }

    /**
     * @Route("/login"),
     * methods={"GET"},
     * name="app_blog_login_login"
     */
    public function login()
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('app_blog_page_index');
        }

        $user = $this->getUserModel();
        $form = $this->createForm(LoginType::class, $user);
        return $this->render('blog/login/login.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/logout"),
     * methods={"GET"},
     * name="app_blog_login_logout"
     */
    public function logout()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App\Entity\User')->find($this->session->get('user')['id']);
        $user->setOnline(0);
        $em->flush();
        $this->session->remove('user');
        return $this->redirectToRoute('app_blog_page_index');
    }

    /**
     * @Route("/autorization"),
     * methods={"POST"},
     * name="app_blog_login_autorization"
     */
    public function autorization(Request $request)
    {
        $user = $this->getUserModel();
        $form = $this->createForm(LoginType::class, $user, array(
            'validation_groups' => array('registration'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $salt = $em->getRepository('App\Entity\User')->getUserSalt($user->getEmail());

            if (!$salt) {
                $this->get('session')->getFlashBag()->add('register-error-notice', 'This email not exist');
                return $this->redirectToRoute('app_blog_login_login');
            }

            $password = md5(md5($user->getPassword() . $this->container->getParameter('app_blog_registration_salt'))) . $salt[0]['salt'];

            $validateUser = $em->getRepository('App\Entity\User')
                ->getUser($user->getEmail(), $password);

            if ($validateUser) {
                $this->session->set('user', $validateUser[0]);
                $user = $em->getRepository('App\Entity\User')->find($validateUser[0]['id']);
                $user->setOnline(1);
                $em->flush();
                return $this->redirectToRoute('app_blog_page_index');
            } else {
                $this->get('session')->getFlashBag()->add('register-error-notice', 'Password is incorrect');
                return $this->redirectToRoute('app_blog_login_login');
            }
        }
    }

    /**
     * @Route("/singup"),
     * methods={"GET"},
     * name="app_blog_login_singup"
     */
    public function singup()
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('app_blog_page_index');
        }

        $user = $this->getUserModel();
        $form = $this->createForm(SingUpType::class, $user);

        return $this->render('blog/login/singup.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/registry"),
     * methods={"POST"},
     * name="app_blog_login_registry"
     */
    public function registry(Request $request)
    {
        $user = $this->getUserModel();
        $form = $this->createForm(SingUpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $checkUser = $em->getRepository('App\Entity\User')->isUserExist($user->getEmail());

            if ($checkUser) {
                $this->get('session')->getFlashBag()->add('register-error-notice', 'This email already exist');

                return $this->redirectToRoute('app_blog_login_singup');
            }

            $salt = md5(md5($this->getRandomSalt()));
            $password = md5(md5($user->getPassword() . $this->container->getParameter('app_blog_registration_salt'))) . $salt;
            $user->setSalt($salt);
            $user->setPassword($password);
            $user->setImage($this->container->getParameter('app_blog_user_default_image'));

            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('register-success-notice', 'You have successfully registered');

            return $this->redirectToRoute('app_blog_login_login');
        }

        $this->get('session')->getFlashBag()->add('register-error-notice', 'Register failed');

        return $this->redirectToRoute('app_blog_login_registry');
    }

    public function getRandomSalt()
    {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $strlength = strlen($characters);
        $random = '';

        for ($i = 0; $i < 10; $i++) {
            $random .= $characters[rand(0, $strlength - 1)];
        }

        return $random;
    }

    public function getUserModel()
    {
       if (null === $this->user) {
           $this->user = new User();
       }

       return $this->user;
    }
}