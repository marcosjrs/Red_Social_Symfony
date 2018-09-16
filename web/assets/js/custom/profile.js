$(document).ready(function(){
    //https://github.com/webcreate/infinite-ajax-scroll
    var ias = jQuery.ias({
        container:  '.user-publications .box-content', //clase del contenedor de lista de items
        item:       '.publication-item', //clase del div que muestra los datos de cada item
        pagination: '.pagination',
        next: '.pagination .next_link',//clase del link de "siguiente", utilizará el href de ese selector
        triggerPageThreshold: 5//cada cuantos lanzara la petición ajax para cargar más items
      });
    
      ias.extension(new IASTriggerExtension({offset: 3, text:"Ver más"}));//cada bloque de 3 items nos mostrará el "ver más"
      ias.extension(new IASSpinnerExtension({src: URL_PRELOADER_USER}));
      ias.extension(new IASNoneLeftExtension({text: "No hay más personas"}));

      //escuchamos la carga de los bloques de items, para cargar los listeners
      ias.on('ready', function(event){ actionsButtons() });
      ias.on('rendered', function(event){ actionsButtons() });
});

function actionsButtons(){
    $(".btn-follow").unbind("click").click(function(){
        $(this).addClass("hidden"); //Una vez que se está siguiendo no se debe mostrar este boton, se debe mostrar la accion contraria. (Como un switch)
        $(this).parent().find(".btn-unfollow").removeClass("hidden");
        var followed = $(this).attr("data-followed");
        $.ajax({
            url: URL_FOLLOW_SERVICE,
            type:'POST',
            data: { followed:followed },
            success:function(response){ }
        });
    });
    $(".btn-unfollow").unbind("click").click(function(){
        $(this).addClass("hidden"); //Una vez que se está siguiendo no se debe mostrar este,  se debe mostrar la accion contraria.
        $(this).parent().find(".btn-follow").removeClass("hidden");
        var followed = $(this).attr("data-followed");
        $.ajax({
            url: URL_UNFOLLOW_SERVICE,
            type:'POST',
            data: { followed:followed },
            success:function(response){ }
        });
    });
}