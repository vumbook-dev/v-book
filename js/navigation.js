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
    window.sendToPage = function(link,loader,data){
        $.ajax({
            method: "POST",
            url: "/pages/"+link+".php",
            data: {data:data},
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

    loadPage("home",vbloader);

    $(document).on("click","li.nav-item > a, a.navbar-brand",function(e){            
            let link = $(this).data("nav");
            let active = $(this).parent("li");
            //window.location.hash = link;
            e.preventDefault();

            if(link == "home"){
                $("li.active").removeClass("active");
                $(".navbar-nav > li:first-child").addClass("active");
                $('title').text(`V-Book`);
                history.replaceState(0, '', './');
            }else{
                $("li.active").removeClass("active");
                active.addClass("active");
            }    

            loadPage(link,vbloader);
    });

});