<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Following;
use AppBundle\Form\RegisterType;
use AppBundle\Form\UserType;

class FollowingController extends Controller
{

    private $session;
    
    public function __construct() {
        $this->session = new Session();//se utilizará para añadir mensages...
    }
    
    
    public function followAction(Request $req)
    {
        $user = $this->getUser();
        $followedId = $req->get('followed'); //id de usuario al que se sigue
        
        $em = $this->getDoctrine()->getManager();
        $repoUser = $em->getRepository("BackendBundle:User");
        $followedUser = $repoUser->find($followedId);//obtenemos el objeto user, porque es lo que se referencia en la entidad de Following.
        
        $following = new Following();
        $following->setUser($user); //usuario "que sigue"
        $following->setFollowed($followedUser);//usuario "seguido"
        
        try{                        
            $em->persist($following);
            $em->flush();
            $status = "Ahora estás siguiendo a {$followedUser->getNick()}";
        }catch(Exception $exc){
            $status = "No se han podido realizar un follow a este usuario";                       
        }
        
        return new Response($status);
        
        
    }
    
    public function unfollowAction(Request $req)
    {
        $user = $this->getUser();
        $followedId = $req->get('followed'); //id de usuario al que se sigue
        
        $em = $this->getDoctrine()->getManager();
        $repoFollow = $em->getRepository("BackendBundle:Following");
        $followed = $repoFollow->findOneBy(
                    array(
                        "user"=>$user->getId(),
                        "followed"=>$followedId
                    )
                );
        
        try{  
            $em->remove($followed);
            $em->flush();
            $status = "Has dejado de seguir a ese usuario";
        }catch(\Doctrine\ORM\ORMInvalidArgumentException $exc2){
            $status = "No se han podido dejar de seguir a este usuario";                       
        }catch(Exception $exc){
            $status = "No se han podido dejar de seguir a este usuario";                       
        }
        
        return new Response($status);
        
        
    }

    /**
     * Objetos following (que representa filas de la tabla Followings, que contiene campos followed y user), de forma paginada, de un user con un nick concreto.
     */
    public function followingAction(Request $request, $nickname = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($nickname != null){
            $userRepo = $em->getRepository("BackendBundle:User");
            $user = $userRepo->findOneBy(array("nick"=>$nickname));
        }else{
            $user = $this->getUser();
        }

        $userId = $user->getId();
        $query = $em->createQuery("Select f FROM BackendBundle:Following f WHERE f.user=$userId ORDER BY f.id DESC");
        //Utilizamos el paginador para obtener los datos de la query de forma paginada.
        $paginator = $this->get("knp_paginator");
        $numItemsShowForPage = 5;
        $pagination = $paginator->paginate($query, $request->query->getInt('page',1), $numItemsShowForPage);
        
        return $this->render('@App/Following/following.html.twig', array(
            'profile_user'=>$user,
            'pagination' => $pagination,
            'type'=> 'following') );//el type indicará si es que estamos siguiendo ("following") o que nos siguen ("followed") y así poder reutilizar la vista
        
        
    }

     /**
     * Objetos following (que representa filas de la tabla Followings, que contiene campos followed y user), de forma paginada, de un user con un nick concreto.
     */
    public function followedAction(Request $request, $nickname = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($nickname != null){
            $userRepo = $em->getRepository("BackendBundle:User");
            $user = $userRepo->findOneBy(array("nick"=>$nickname));
        }else{
            $user = $this->getUser();
        }

        $userId = $user->getId();
        $query = $em->createQuery("Select f FROM BackendBundle:Following f WHERE f.followed=$userId ORDER BY f.id DESC");
        //Utilizamos el paginador para obtener los datos de la query de forma paginada.
        $paginator = $this->get("knp_paginator");
        $numItemsShowForPage = 5;
        $pagination = $paginator->paginate($query, $request->query->getInt('page',1), $numItemsShowForPage);
        
        return $this->render('@App/Following/following.html.twig', array(
            'profile_user'=>$user,
            'pagination' => $pagination,
            'type'=> 'followed') );//el type indicará si es que estamos siguiendo ("following") o que nos siguen ("followed") y así poder reutilizar la vista
        
        
    }
    
    
}
