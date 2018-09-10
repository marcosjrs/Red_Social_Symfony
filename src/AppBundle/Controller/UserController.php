<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\User;
use \AppBundle\Form\RegisterType;

class UserController extends Controller
{
    public function loginAction(\Symfony\Component\HttpFoundation\Request $req)
    {
        return $this->render('@App/User/login.html.twig');
    }
    
    public function registerAction(\Symfony\Component\HttpFoundation\Request $req)
    {
        $user = new User();        
        
        $form = $this->createForm(RegisterType::class, $user);
       
        
        return $this->render('@App/User/register.html.twig', array(
            "form" => $form->createView()
        ));
    }
}
