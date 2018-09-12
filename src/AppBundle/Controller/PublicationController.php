<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PublicationController extends Controller
{
    public function indexAction()
    {
        if( !is_object($this->getUser()) ){
            return $this->redirect("login");
        }
         return $this->render('@App/Publication/home.html.twig');
    }
}
