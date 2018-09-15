<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\PublicationType;
use BackendBundle\Entity\Publication;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicationController extends Controller {

    private $session;
    
    public function __construct() {
        $this->session = new Session();//se utilizará para añadir mensages...
    }
    
    public function indexAction(Request $request) {
        if (!is_object($this->getUser())) {
            return $this->redirect("login");
        }
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);

        $form->handleRequest($request); //Bindamos el objeto $request, con las modificaciones en el formulario.

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                
                //Para la imagen
                $file = $form['image']->getData();
                if (!empty($file) && $file != null) {
                    $ext = $file->guessExtension();// recogemos la extensión, para añadirsela al guardado.
                }
                if (!empty($file) && $file != null && ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif')) {
                    $file_name = $user->getId() . time() . "." . $ext;//nombre que le daremos a la imagen guardada
                    $file->move("uploads/publications/images", $file_name);
                    $publication->setImage($file_name);//para guardar la ruta en la tabla
                } else {
                    $publication->setImage(null);
                }
                
                //Para el documento
                $file = $form['document']->getData();
                if (!empty($file) && $file != null) {
                    $ext = $file->guessExtension();// recogemos la extensión, para añadirsela al guardado.
                }
                if (!empty($file) && $file != null && $ext == 'pdf') {
                    $file_name = $user->getId() . time() . "." . $ext;//nombre que le daremos a la imagen guardada
                    $file->move("uploads/publications/documents", $file_name);
                    $publication->setDocument($file_name);//para guardar la ruta en la tabla
                } else {
                    $publication->setDocument(null);
                }
                
                //seteamos el resto de los datos en la nueva publicación
                $publication->setUser($user);
                $publication->setCreatedAt(new \DateTime("now"));
                
                try{
                    $em->persist($publication);
                    $em->flush();
                    $status = "Publicación creada correctamente";
                }catch(\Doctrine\ORM\ORMInvalidArgumentException $exc2){
                    $status = "No se han podido guardar la publicación";                       
                }catch(Exception $exc){
                    $status = "No se han podido guardar la publicación";                       
                }
                
            } else {
                $status = "El formulario no se ha rellenado correctamente";
            }
            $this->session->getFlashBag()->add("status",$status);//añadimos en los "mensajes flash" el indice "status"
            return $this->redirectToRoute('home_publications');
            
        }
        
        $publications = $this->getPublications($request);

        return $this->render('@App/Publication/home.html.twig', 
                array('form' => $form->createView(), 'pagination'=> $publications));
    }
    
    /**
     * Devolverá paginados todos "nuestros" mensajes (del user logado) y tambien los mensajes de los users que seguimos
     * Esta función no es un Action, se llamará internamente.
     * @param Request $request que puede contener el parametro page, indicando que pagina está "cargando"
     */
    public function getPublications(Request $request){
        $user = $this->getUser();
        //Intentaremos reproducir la sentencia:
        //select * from publications 
        //    where user_id = 1 
        //    or user_id in (select followed from following where user = 1  );
        $em = $this->getDoctrine()->getManager();
        $publicationRepo = $em->getRepository("BackendBundle:Publication");
        $followingRepo = $em->getRepository("BackendBundle:Following");
        
        //Recogemos los ids. Sería la parte (select followed from following where user = 1  )
        $userFollowing = $followingRepo->findBy(array('user'=>$user)); //seguimientos ("filas") del usuario logado
        $usersFollowed = [];
        foreach ($userFollowing as $follow) {
            $usersFollowed[] = $follow->getFollowed();
        }
        //ya tenemos todos los usuarios que seguimos, recordando que queremos mostrar tambien sus mensajes
        $query = $publicationRepo->createQueryBuilder('p') //p como alias de publication
                ->where('p.user = :userId OR p.user IN (:usersFollowed)')
                ->setParameter('userId',$user->getId())
                ->setParameter('usersFollowed', $usersFollowed)
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        
        $paginator = $this->get('knp_paginator');
        $page = $request->query->getInt('page',1);//carga el parametro page, por defecto será 1
        $pagination = $paginator->paginate( $query, $page, 5 );//5 elementos por bloque
        return $pagination;
    }

    public function removePublicationAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $publicationRepo = $em->getRepository("BackendBundle:Publication");
        $publication = $publicationRepo->find($id);

        if($publication->getUser()->getId() == $this->getUser()->getId()){
            try{
                $em->remove($publication);
                $em->flush();
                $status = 'Publicación borrada correctamente';
            }catch(\Doctrine\ORM\ORMInvalidArgumentException $exc2){
                $status = 'No se han podido borrar la publicación';
            }catch(Exception $exc){
                $status = 'No se han podido borrar la publicación';
            }
        }else{
            $status = 'No se han podido borrar la publicación';
        }        

        return new Response($status);

    }

}
