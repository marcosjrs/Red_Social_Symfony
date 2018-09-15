$(document).ready(function(){
    //https://github.com/webcreate/infinite-ajax-scroll
    var ias = jQuery.ias({
        container:  '#timeline .box-content', //clase del contenedor de lista de publicaciones
        item:       '.publication-item', //clase del div que muestra la publicación de cada usuario
        pagination: '#timeline .pagination',
        next: '#timeline .pagination .next_link',//clase del link de "siguiente", utilizará el href de ese selector
        triggerPageThreshold: 5//cada cuantos lanzara la petición ajax para cargar más usuarios
      });
    
      ias.extension(new IASTriggerExtension({offset: 3, text:"Ver más"}));//cada bloque de 3 publicaciones nos mostrará el "ver más"
      ias.extension(new IASSpinnerExtension({src: URL_PRELOADER_USER}));
      ias.extension(new IASNoneLeftExtension({text: "No hay más publicaciones"}));

      //escuchamos la carga de los bloques de publicaciones, para cargar los listeners
      ias.on('ready', function(event){ publicationButtons() });
      ias.on('ready', function(event){ publicationButtons() });
});

function publicationButtons(){
   $(".toggle-visibility-img").unbind("click").click(function(){
       $(this).parent().find(".container-img-publication").fadeToggle();
   });
}