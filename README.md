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