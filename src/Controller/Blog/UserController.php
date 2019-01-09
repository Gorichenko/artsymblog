<?php

namespace App\Controller\Blog;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use App\Form\Blog\EditAccountType;
use Symfony\Component\Filesystem\Filesystem;
use App\Service\UploadService;

class UserController extends Controller
{
    protected $filesystem;
    protected $session;
    protected $user = null;

    public function __construct(
        Filesystem $filesystem,
        SessionInterface $session
    )
    {
        $this->filesystem = $filesystem;
        $this->session = $session;
    }

    /**
     * @Route("/my_account"),
     * methods="GET",
     * name="app_blog_user_account"
     */
    public function account()
    {
        return $this->render('blog/user_account/user_account.html.twig');
    }

    /**
     * @Route("/edit_account"),
     * methods="GET",
     * name="app_blog_user_edit"
     */
    public function edit()
    {
        $userEntity = $this->getUserModel();
        $form = $this->createForm(EditAccountType::class, $userEntity);

        return $this->render('blog/user_account/edit_account.html.twig', array(
            'user' => $userEntity,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/save_account"),
     * methods="POST",
     * name="app_blog_user_save"
     */
    public function save(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->session->get('user')['id']);
        $uploadService = new UploadService($this->get('kernel')->getProjectDir() . UploadService::IMG_DIR);
        $form = $this->createForm(EditAccountType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $checkUser = $em->getRepository('App\Entity\User')->isUserExist($user->getEmail());

            if ($checkUser && $checkUser[0]['email'] != $this->session->get('user')['email']) {
                $this->get('session')->getFlashBag()->add('register-error-notice', 'This email already exist');

                return $this->redirectToRoute('app_blog_user_edit');
            }

            $salt = md5(md5($this->getRandomSalt()));
            $password = md5(md5($form->getData()->getPassword() .
                    $this->container->getParameter('app_blog_registration_salt'))) . $salt;
            $user->setSalt($salt);
            $user->setPassword($password);

            try {
                $fileName = $uploadService->upload($request->files->get(EditAccountType::BLOCK_PREFIX)['file'],
                    $this->session->get('user')['id']);
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('register-error-notice', $e->getMessage());

                return $this->redirectToRoute('app_blog_user_edit');
            }
            $user->setName($form->getData()->getName());
            $user->setSurname($form->getData()->getSurname());
            $user->setEmail($form->getData()->getEmail());
            $user->setImage($fileName);

            $em->persist($user);
            $em->flush();

            $this->updateUserInfo($user);

            $this->get('session')->getFlashBag()->add('register-success-notice', 'You have successfully edit account');

            return $this->redirectToRoute('app_blog_user_account');
        }

        $this->get('session')->getFlashBag()->add('register-error-notice', 'Edited failed');

        return $this->redirectToRoute('app_blog_user_edit');
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

    public function updateUserInfo($user)
    {
        $this->session->set('user',
            [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'image' => $user->getImage(),
                'email' => $user->getEmail()
            ]);
    }

    public function getUserModel()
    {
        if (null === $this->user) {
            $this->user = new User();
        }

        return $this->user;
    }
}