jQuery(document).ready(function($){

    const loader = '<div class="col vb-loading"><div class="lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>';

    function loadPage(link,loader){
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
    }

    loadPage("home",loader);

    $(document).on("click","li.nav-item > a, a.navbar-brand",function(e){            
            let link = $(this).data("nav");
            let active = $(this).parent("li");
            //window.location.hash = link;
            e.preventDefault();

            if(link == "home"){
                $("li.active").removeClass("active");
                $(".navbar-nav > li:first-child").addClass("active");
                history.replaceState(0, '', './');
            }else{
                $("li.active").removeClass("active");
                active.addClass("active");
            }    

            loadPage(link,loader);
    });

});