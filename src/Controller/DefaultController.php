<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\GreetingGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DefaultController extends Controller
{
   /**
   * @Route("/hello")
   */
    public function index(GreetingGenerator $generator)
    {
        $greeting = $generator->getRandomGreeting();
        return $this->render('default/page.html.twig', [
            'name' => $generator,
        ]);
    }

    /**
     * @Route("/sayHi")
     */
    public function sayHi(SessionInterface $session)
    {

        $orderNum = new Response($session->get('orderNumber'));
        return $this->render('default/order.html.twig');
    }

    /**
     * @Route("/simplicity")
     */
    public function simple(SessionInterface $session)
    {
        $session->set('orderNumber', 'Tdf5473');
        $this->addFlash(
            'notice',
            'Изменения сохранены!'
        );
        return $this->redirect('http://0.0.0.0:7177/sayHi');
    }

    /**
     * @Route("/api/hello/{name}")
     */
    public function apiExample($name)
    {
        return $this->json([
            'name' => $name,
            'symfony' => 'rocks',
        ]);
    }
}