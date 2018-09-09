# Red Social Symfony


Es un proyecto con un fin educativo (el de refrescar mis conocimientos). Está creado con Symfony, JQuery y Bootstrap.

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

3. Modificar la llamada de renderización en el DefaultContoller del bundle creado, ya que no encontraba el twig, de la forma creada con el comando:
`return $this->render('@Backend/Default/index.html.twig');`



