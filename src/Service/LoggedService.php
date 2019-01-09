<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class LoggedService
{
    public function isLoggedIn()
    {
        $session = new Session();
        return $session->get('user') ? true : false;
    }
}
