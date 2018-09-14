$(document).ready(function(){
    //https://github.com/webcreate/infinite-ajax-scroll
    var ias = jQuery.ias({
        container:  '.box-users', //clase del contenedor de lista de usuarios
        item:       '.user-item', //clase del div que muestra los datos de cada usuario
        pagination: '.pagination',
        next: '.pagination .next_link',//clase del link de "siguiente", utilizará el href de ese selector
        triggerPageThreshold: 5//cada cuantos lanzara la petición ajax para cargar más usuarios
      });
    
      ias.extension(new IASTriggerExtension({offset: 3, text:"Ver más"}));//cada bloque de 3 usuarios nos mostrará el "ver más"
      ias.extension(new IASSpinnerExtension({src: URL_PRELOADER_USER}));
      ias.extension(new IASNoneLeftExtension({text: "No hay más personas"}));
});