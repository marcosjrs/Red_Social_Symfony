<?php
namespace AppBundle\Twig;

class UserStatsExtension extends \Twig_Extension {
    protected $doctrine;
    
    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
    * Indica como se llama el filtro de twig (en este caso 'user_stats' y a que función se llamará, en este caso userStatsFilter)
    * Ejemplo de uso en .twig:  app.user|user_stats()
    */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('user_stats', array($this,'userStatsFilter'))
        );
    }  
    
    /**
     * Devolvera información de numero de 'seguidores', 'siguiendo', 'publicaciones' y 'me gustas'.
     * @param type $user
     * @return array con los atributos 'following','followers','publications' y 'likes'
     */
    public function userStatsFilter($user){
        $like_repo = $this->doctrine->getRepository('BackendBundle:Like');
        $following_repo = $this->doctrine->getRepository('BackendBundle:Following');
        $publication_repo = $this->doctrine->getRepository('BackendBundle:Publication');
       
        $user_following = $following_repo->findBy(array( "user"=>$user ));//users que sigue
        $user_followers = $following_repo->findBy(array( "followed"=>$user )); //users que le siguen
        $user_publications = $publication_repo->findBy(array( "user"=>$user ));//publicaciones
        $user_likes = $like_repo->findBy(array( "user"=>$user ));//publicaciones
        
        $result = array(
            'following' => count($user_following),
            'followers' => count($user_followers),
            'publications' => count($user_publications),
            'likes' => count($user_likes)
        );

        return $result;
    }
    
    public function getName() {
        return 'user_stats_extension';
    }

}
