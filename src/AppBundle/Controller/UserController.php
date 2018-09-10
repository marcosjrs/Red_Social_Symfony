<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\User;
use \AppBundle\Form\RegisterType;

class UserController extends Controller
{
    private $session;
    
    public function __construct() {
        $this->session = new Session();//se utilizará para añadir mensages...
    }
    
    public function loginAction(Request $req)
    {
        return $this->render('@App/User/login.html.twig');
    }
    
    public function registerAction(Request $req)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user); //se rellenará en user los datos rellenados en el form, cuando se haga submit.
        $form->handleRequest($req);
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
                        $status = "No se han podido guardar los datos del usuario.";                       
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
}
