<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository("BackendBundle:User");
        $user = $userRepo->find(1);
        return $this->render('@Backend/Default/index.html.twig', [
            'nombre' => $user->getName(),
        ]);
    }
}
