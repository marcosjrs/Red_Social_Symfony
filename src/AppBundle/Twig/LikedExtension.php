<?php
namespace AppBundle\Twig;

class LikedExtension extends \Twig_Extension {
    protected $doctrine;
    
    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
    * Indica como se llama el filtro de twig (en este caso 'like' y a que función se llamará, en este caso likeFilter)
    * Ejemplo de uso en .twig:  {if app.user|like(user) == true %} ...  {% endif%}
    */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('liked', array($this,'likeFilter'))
        );
    }  
    
    /**
     * Comprueba si un usuario tiene un like asignado a una publicación.
     * @param type $userId
     * @param type $followedId
     * @return boolean
     */
    public function likeFilter($userId, $publication){
        $like_repo = $this->doctrine->getRepository('BackendBundle:Like');
        $publication_liked = $like_repo->findOneBy(array(
                                "user"=>$userId,
                                "publication"=>$publication
                            ));
        if(!empty($publication_liked) && is_object($publication_liked)){
            $like = true;
        }else{
            $like = false;
        }
        return $like;
    }
    
    public function getName() {
        return 'like_extension';
    }

}
