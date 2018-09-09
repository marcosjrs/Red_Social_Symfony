<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function loginAction(\Symfony\Component\HttpFoundation\Request $req)
    {
        return $this->render('@App/User/login.html.twig');
    }
}
