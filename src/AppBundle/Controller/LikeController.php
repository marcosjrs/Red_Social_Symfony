<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\PublicationType;
use BackendBundle\Entity\Publication;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Like;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LikeController extends Controller {

    private $session;
    
    public function __construct() {
        $this->session = new Session();//se utilizará para añadir mensages...
    }   

    public function likeAction($id = null){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $publicationRepo = $em->getRepository("BackendBundle:Publication");
        $publication = $publicationRepo->find($id);

        $like = new Like();
        $like->setUser($user);
        $like->setPublication($publication);

        try{
            $em->persist($like);
            $em->flush();
            $status = 'Like dado correctamente';
        }catch(\Doctrine\ORM\ORMInvalidArgumentException $exc2){
            $status = 'No se pudo dar un like a la publicación';
        }catch(Exception $exc){
            $status = 'No se pudo dar un like a la publicación';
        }
    

        return new Response($status);

    }

    public function unlikeAction($id = null){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $likeRepo = $em->getRepository("BackendBundle:Like");
        $like = $likeRepo->findOneBy(array(
            "user"=>$user,
            "publication"=>$id
        ));

        try{
            $em->remove($like);
            $em->flush();
            $status = 'Like borrado correctamente';
        }catch(\Doctrine\ORM\ORMInvalidArgumentException $exc2){
            $status = 'No se pudo borrar el like a la publicación';
        }catch(Exception $exc){
            $status = 'No se pudo borrar el like a la publicación';
        }
    

        return new Response($status);

    }

}
