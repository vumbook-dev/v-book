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

    //loadPage("home",vbloader);

    // $(document).on("click","li.nav-item > a, a.navbar-brand",function(e){            
    //         let link = $(this).data("nav");
    //         let active = $(this).parent("li");
    //         //window.location.hash = link;
    //         e.preventDefault();

    //         if(link == "home"){
    //             $("li.active").removeClass("active");
    //             $(".navbar-nav > li:first-child").addClass("active");
    //             $('title').text(`V-Book`);
    //             history.replaceState(0, '', './');
    //         }else{
    //             $("li.active").removeClass("active");
    //             active.addClass("active");
    //         }    

    //         loadPage(link,vbloader);
    // });

});