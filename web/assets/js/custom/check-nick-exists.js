$(document).ready(function(){
    $(".nick-input").blur(function(){
        var nick = this.value;
        var input = this;
        $.ajax({
            url: URL_CHECK_NICK_EXISTS,
            data:{nick : nick},
            type:'POST',
            success:function(response){
                if(response == "used"){
                    $(input).css("border","1px solid red");
                }else{
                    $(input).css("border","1px solid green");
                }
            }
        })
    });
});

