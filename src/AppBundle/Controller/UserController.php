<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use BackendBundle\Entity\User;
use AppBundle\Form\RegisterType;
use AppBundle\Form\UserType;

class UserController extends Controller
{
    private $session;
    
    public function __construct() {
        $this->session = new Session();//se utilizará para añadir mensages...
    }
    
    public function loginAction(Request $req)
    {
        //si ya está logado...
        if( is_object($this->getUser()) ){
            return $this->redirect("home");
        }
        $authUtils = $this->get('security.authentication_utils'); // $authUtils es de tipo AuthenticationUtils
        $lastAuthError = $authUtils->getLastAuthenticationError(); // comprueba si viene en el request, luego comprueba si está en session
        $lasUsername = $authUtils->getLastUsername();// comprueba si viene en el request, luego comprueba si está en session
        
        return $this->render('@App/User/login.html.twig',array(
            'last_username'=>$lasUsername,
            'last_error'=>$lastAuthError
        ));
    }
    
    
    public function registerAction(Request $req)
    {
        //si ya está logado...
        if( is_object($this->getUser()) ){
            return $this->redirect("home");
        }
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user); //se rellenará el form con los datos
        $form->handleRequest($req); //se bindeará las modificaciones hechas, en el formulario, y el $user utilizado para rellenar.
        $status = "";
        if($form->isSubmitted()){
            if($form->isValid()){
                
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick')                        
                        ->setParameter('email', $form->get('email')->getData())
                        ->setParameter('nick', $form->get('nick')->getData());
                $result = $query->getResult();
                if(count($result) == 0){ //no existe y se guarda con pass encriptada
                    
                    $encoders = $this->get("security.encoder_factory");// para traer los encoders configurados (ver security.yml)
                    $encoder = $encoders->getEncoder($user);// Recogeríamos el algoritmo de encriptado configurado para BackendBundle\Entity\User
                    $password = $encoder->encodePassword($form->get("password")->getData(),$user->getSalt());
                    
                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImage(null); 
                    
                    try{                        
                        $em->persist($user);
                        $em->flush();
                        return $this->redirect("login");
                    }catch(Exception $exc){
                        $status = "No se han podido guardar los datos correctamente";                       
                    }
                    
                }else{
                    $status = "Este usuario ya existe.";      
                }
                        
            }else{
                $status = "El formulario no se ha rellenado correctamente."; 
            }
            
            
            $this->session->getFlashBag()->add("status", $status);
                
        }
        
        return $this->render('@App/User/register.html.twig', array(
            "form" => $form->createView()
        ));
        
    }
    
    public function checkNickExistsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository("BackendBundle:User");
        $user = $userRepo->findOneBy(array("nick"=>$request->get("nick")));
        
        return new Response( $user ? "used" : "not used" );        
    }
    
    public function editUserAction(Request $req){
        $user = $this->getUser();//obtenemos los datos del usuario actual.
        $imagenInicial = $user->getImage();//nombre del archivo inicialmente.
        $form = $this->createForm(UserType::class, $user); //se rellenará el form con los datos
        $form->handleRequest($req); //se bindeará las modificaciones hechas, en el formulario, y el $user utilizado para rellenar.
        
        $status = "";
        if($form->isSubmitted()){
            if($form->isValid()){ 
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick')                        
                        ->setParameter('email', $form->get('email')->getData())
                        ->setParameter('nick', $form->get('nick')->getData());
                $result = $query->getResult();
                $sonSusDatos = ($form->get('nick')->getData() == $user->getNick()) && ($form->get('email')->getData() == $user->getEmail());
                if( $sonSusDatos || ( count($result) == 0) ){ //el email y nick son de de él o no existe nadie con esos datos.                  
                    //upload file
                    $file = $form["image"]->getData();
                    if(!empty($file) && $file!=null){
                        $ext = $file->guessExtension();
                        if($ext == 'jpg' || $ext == 'jpeg' ||$ext == 'png'|| $ext=='gif'){
                            $file_name= $user->getId().time().'.'.$ext;
                            $file->move("uploads/users",$file_name);//guardamos el archivo en el directorio de uploads/users
                            $user->setImage($file_name);
                        }                        
                    }else{
                        $user->setImage($imagenInicial);
                    }
                    //persistencia
                    try{                        
                        $em->persist($user);
                        $em->flush();
                        $status = "Has modificado los datos correctamente.";
                    }catch(Exception $exc){
                        $status = "No se han podido guardar los datos correctamente.";                       
                    } 
                }else{
                    $status = "Nick o email no válido";       
                }
            }else{
                $status = "El formulario no se ha rellenado correctamente."; 
            }
            $this->session->getFlashBag()->add("status", $status);  
        }
        return $this->render('@App/User/edit_user.html.twig', array(
            "form" => $form->createView()
        ));
    }
}
