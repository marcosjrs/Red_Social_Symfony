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

    /**
     * Objetos Like ( que contiene campos publicacion y user), de forma paginada, de un user con un nick concreto.
     */
    public function likesAction (Request $request, $nickname = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($nickname != null){
            $userRepo = $em->getRepository("BackendBundle:User");
            $user = $userRepo->findOneBy(array("nick"=>$nickname));
        }else{
            $user = $this->getUser();
        }

        $userId = $user->getId();
        $query = $em->createQuery("Select l FROM BackendBundle:Like l WHERE l.user=$userId ORDER BY l.id DESC");
        
        //Utilizamos el paginador para obtener los datos de la query de forma paginada.
        $paginator = $this->get("knp_paginator");
        $numItemsShowForPage = 5;
        $pagination = $paginator->paginate($query, $request->query->getInt('page',1), $numItemsShowForPage);
        
        return $this->render('@App/Like/like.html.twig', array(
            'profile_user'=>$user,
            'pagination' => $pagination ) );
        
        
    }

}
