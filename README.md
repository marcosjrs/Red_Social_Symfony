# Red Social Symfony


Es un proyecto con un fin educativo (el de refrescar mis conocimientos). Está creado con Symfony, JQuery y Bootstrap. Con vistas acopladas (renderizado desde servidor).

La base del proyecto es la symfony/framework-standard-edition, creado con composer a partir de la instrucción: 

`composer create-project symfony/framework-standard-edition red-social-symfony`

Enlaces de interés sobre la versión de symfony utilizada:

- https://symfony.com/doc/3.4/setup.html
- https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/index.html
- https://symfony.com/doc/3.4/doctrine.html
- https://symfony.com/doc/3.4/templating.html
- https://symfony.com/doc/3.4/security.html
- https://symfony.com/doc/3.4/email.html
- https://symfony.com/doc/3.4/logging.html
- https://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html
- https://symfony.com/doc/current/setup/built_in_web_server.html
- https://symfony.com/doc/current/setup.html

# Notas

## Problemas al crear los bundles por comandos
Tras ejecutar el comando correspondiente para añadir el bundle, en este caso uno llamado BackendBundle (que era el primer bundle creado) :
`php .\bin\console generate:bundle`

Lo que hice fue:
1. Sustituir el contenido del "psr-4" del composer.json, por:
`"psr-4": {
            "": "src/"
        }`

2. Ejecutar el comando: 
`composer update`

3. Modificar la llamada de renderización en el DefaultContoller del bundle creado (ya que de otra forma no encontraba el twig):
`return $this->render('@Backend/Default/index.html.twig');`

## Comando para traer las tablas creando las entities correspondientes (previa creación de BBDD, y posterior configuración del proyecto cuando se ha creado)

1. Generar la configuración en yml

`php .\bin\console doctrine:mapping:import BackendBundle yml`

2. Tras la modificación de la configuración de los yml (por ejemplo para poner los nombres en singular de las entities), actualizamos 
`php bin/console doctrine:schema:update --force`

3.  crear las entidades con esa configuración
`php .\bin\console doctrine:generate:entities BackendBundle`

## Ejemplo para ejecutar los tests de un bundle, por ejemplo el 'BackendBundle'
`.\vendor\bin\simple-phpunit .\tests\BackendBundle\`

## Ejemplo de ruteado del proyecto con yml

Se ha cargado en el routing.yml de la aplicación(app\config), el routing.xml del /Resources/config del AppBundle, del siguiente modo:
```
app:
    resource: '@AppBundle/Resources/config/routing.yml'
    prefix: /
```

En ese routing.yml (del AppBundle) a su vez  se indica quien se responsabiliza del path de la home / y se cargan el resto(donde tambien se indicarn el path y el action correspondiente)

```
app_homepage:
    path:     /
    defaults: { _controller: AppBundle:User:login }
app_user:
    resource: "@AppBundle/Resources/config/routing/user.yml"
    prefix: /
app_publication:
    resource: "@AppBundle/Resources/config/routing/publication.yml"
    prefix: /
....
```

## Ejemplo utilizando layouts

En el layout tenemos por ejemplo :
```
<section>
    {% block content %}
    {# Para incluir todo el contenido #}
    {% endblock %}
</section>
```

Cuando se quiere utilizar, se puede hacer del siguiente modo:
```
{% extends "@App/Layouts/layout.html.twig" %}
{% block content %}
Este es el contenido que se mostrará
{% endblock %}
```

## Configuración de un encoder con el algoritmo bcrypt para una Entidad en concreto

1. Por ejemplo, para configurar que en la entidad BackendBundle\Entity\User se usará un encoder con el algoritmo bcrypt ( y 4 veces encriptado), lo establecemos dentro de security > enconders del archivo security.yml de la siguiente forma:
```
        BackendBundle\Entity\User:
            algorithm: bcrypt
            cost: 4 
```
2. Luego, en el futuro podremos "recoger" el algoritmo de encriptado asignado a BackendBundle\Entity\User y utilizarlo para encriptar, por ejemplo en un Action de la siguiente forma:
```
$user = new User(); //Instancia de BackendBundle\Entity\User
$encoders = $this->get("security.encoders_factory");
$encoder = $encoders->getEncoder($user); // Recogeríamos el algoritmo de encriptado configurado para BackendBundle\Entity\User
$passEncriptada = $encoder->encodePassword($passSinEncriptar, null);// Encriptariamos con esa configuracion
      
```

## Ejemplo de configuración de login

En el security.yml se añade en security > providers:
```
    #Indicamos cual va a ser el provider de la autentificacion, es decir, que entidad nos va a proporcionar la autentificacion del usuario donde como 'username' se utilizará el email. 
    #Para esto User debe implementar Symfony\Component\Security\Core\User\UserInterface, e implementar  getCredentials, getUsername (que devolverá el email) y getRoles
    user_db_provider:
        entity:
            class: BackendBundle:User
            property: email
```

Y en el mismo archivo, en security > firewalls > main añadimos:
```
    # https://symfony.com/doc/current/security/form_login_setup.html
    # check_path es el path encargada de la comprobacion automatica del formulario
    # target indica a donde nos va a llevar cuando salga de la sesion.
    provider: user_db_provider
    form_login:
        login_path: /login
        check_path: /login_check
    logout:
        path: logout
        target: /
```

Obviamente luego hay que implementar los actions correspondientes en el "BackendBundle:User" y  añadir en el user.yml las nuevas rutas a dichos actions. Para finalmente crear el formulario utilizando las variables correspondientes.
Por ejemplo:
```
public function loginAction(Request $req)
{
    $authUtils = $this->get('security.authentication_utils'); // $authUtils es de tipo AuthenticationUtils
    $lastAuthError = $authUtils->getLastAuthenticationError(); // comprueba si viene en el request, luego comprueba si está en session
    $lasUsername = $authUtils->getLastUsername();// comprueba si viene en el request, luego comprueba si está en session
    
    return $this->render('@App/User/login.html.twig',array(
        'last_username'=>$lasUsername,
        'last_error'=>$lastAuthError
    ));
}
```
Y por ejemplo, el form añadido en la vista (de tipo .twig):
```
<form action="{{path('login_check')}}" method="POST">
    <label>Email</label><input type="text" id="username" name="_username" value="{{last_username}}" class="form-control">
    <label>Contraseña</label><input id="password" name="_password" type="password" class="form-control">
    <input type="submit" value="Entrar" class="btn btn-success">
    <input type="hidden" name="_target_path" value="/home">
</form>
```
Con esto automáticamente recoge el usuario del modelo, con el email introducido, y comprueba que la pass está correcta, si es así redirige a /home
Y si tenemos un enlace con href="{{path('logout')}}" automáticamente cierra la sesión (no hace falta que implementemos nosotros el action)

## Ejemplo de validación

Creando un archivo llamado validation.yml dentro del Bundle (en este caso AppBundle) en la carpeta Resources\config\, hace la validación automáticamente de las propiedades de la entidad que se configure dentro de este, por ejemplo:
```
BackendBundle\Entity\User:
    properties: 
        name: 
            - NotBlank: {message: "El nombre no puede estar vacío"}
        surname: 
            - NotBlank: {message: "Los apellidos no puede estar vacío"}
        nick: 
            - NotBlank: {message: "El nick no puede estar vacío"}
        email: 
            - NotBlank: {message: "El email no puede estar vacío"}
            - Email: 
                message: El email {{ value }} no es válido.
                # A false para evitar problemas de dominios personalizados, etc
                checkMX: false
```

## Ejemplo de configuración del paginador KnpPaginator
https://github.com/KnpLabs/KnpPaginatorBundle

1. Añadimos el Bundle en el AppKernel.php
```
new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
```
2. Personalizamos (si quisieramos) la vista del paginador, por ejemplo copiando el vendor\knplabs\knp-paginator-bundle\Resources\views\Pagination\twitter_bootstrap_v3_pagination.html
en las vistas de nuestro bundle (modificándolo luego según nuestras necesidades), para luego vincularlo desde la configuración

3. Configuramos en el config.yml (app\config\config.yml)
```
knp_paginator:
    #numero de links enseñados en el menú de paginación
    page_range: 5
    default_options:
        #nombre del parametro indentificador de la página será "page", el de ordenación será "sort"...
        page_name: page
        sort_field_name: sort
        sort_direction_name: direction
        distinct: true
    #personalizaremos la plantilla de paginación (basandose en la propia de vendor\knplabs\knp-paginator-bundle\Resources\views\Pagination\twitter_bootstrap_v3_pagination.html)
    template:
        pagination: '@App/Layouts/custom_pagination.html.twig'
        sortable: '@KnpPaginator/Pagination/sortable_link.html.twig'
```

## Utilización del paginador KnpPaginator tras su configuración
https://github.com/KnpLabs/KnpPaginatorBundle

1.  Utilizamos la paginación sobre una query, por ejemplo
```
public function usersAction(Request $request){
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery("SELECT u FROM BackendBundle:User u");
        
    //Utilizamos el paginador para obtener los datos de la query de forma paginada.
    $paginator = $this->get("knp_paginator");
    $numUserShow = 5;
    $pagination = $paginator->paginate($query, $request->query->getInt('page',1), $numUserShow);
    
    return $this->render('@App/User/users.html.twig', array(
        'pagination' => $pagination
    ));
}
```

2. En la vista lo renderizamos, por ejemplo:
```
<div class="navigation">
    {# Le pasamos el atributo pasado desde el controller pagination #}
    {{knp_pagination_render(pagination)}}             
</div>
```
