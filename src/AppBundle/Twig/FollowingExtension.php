<?php
namespace AppBundle\Twig;

class FollowingExtension extends \Twig_Extension {
    protected $doctrine;
    
    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
    * Indica como se llama el filtro de twig (en este caso 'following' y a que función se llamará, en este caso followingFilter)
    * Ejemplo de uso en .twig:  {if app.user|following(user) == true %} ...  {% endif%}
    */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('following', array($this,'followingFilter'))
        );
    }  
    
    /**
     * Comprueba si un usuario está siguiente a otro.
     * @param type $userId
     * @param type $followedId
     * @return boolean
     */
    public function followingFilter($userId, $followedId){
        $following_repo = $this->doctrine->getRepository('BackendBundle:Following');
        $user_following = $following_repo->findOneBy(array(
                                "user"=>$userId,
                                "followed"=>$followedId
                            ));
        if(!empty($user_following) && is_object($user_following)){
            $following = true;
        }else{
            $following = false;
        }
        return $following;
    }
    
    public function getName() {
        return 'following_extension';
    }

}
