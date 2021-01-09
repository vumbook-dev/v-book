jQuery(document).ready(function($){

    window.vbloader = '<div class="col vb-loading"><div class="lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>';

    //GET PAGE
    window.loadPage = function(link,loader){
        $.ajax({
            method: "GET",
            url: "/pages/"+link+".php",
            beforeSend: function(){
                $("#vb-show-content").html(loader);
            },
            success: function(data){
                setTimeout(function(){
                    $("#vb-show-content").html(data);
                },400);                    
            }
        });
    };

    //SEND DATA TO PAGE
    window.sendToPage = function(link,loader,data,action=""){
        $.ajax({
            method: "POST",
            url: "/pages/"+link+".php",
            data: {data:data,action:action},
            dataType: "text",
            beforeSend: function(){
                $("#vb-show-content").html(loader);
            },
            success: function(data){
                setTimeout(function(){
                    $("#vb-show-content").html(data);
                },400);                    
            }

        })
    }

    //CHECK IF COOKIE EXIST
    window.getCookie = function(name){
        let dc = document.cookie;
        let prefix = name + "=";
        let begin = dc.indexOf("; " + prefix);
        let end;
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        }
        else
        {
            begin += 2;
            end = document.cookie.indexOf(";", begin);
            if (end == -1) {
            end = dc.length;
            }
        }
        // because unescape has been deprecated, replaced with decodeURI
        //return unescape(dc.substring(begin + prefix.length, end));
        return decodeURI(dc.substring(begin + prefix.length, end));
    }

    //MESSAGE FLASH
    window.flashMessage = function(message,type){
        let icon = (type === 'success') ? "fa-check-circle-o" : "fa-exclamation-circle";
        $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-'+type+'" style="display:none;left:100px;" role="alert"><i class="fa '+icon+'" aria-hidden="true"></i> '+message+'</div>');
        $("div.message-status").animate({left:0, opacity:"show"}, 500);
        setTimeout(function(){
            $("div.message-status").fadeOut(1500,function(){
                $("div.message-status").remove();
            });            
        },2500);
    }

});