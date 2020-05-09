jQuery(document).ready(function($){

    const loader = '<div class="col vb-loading"><div class="lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>';

    $(document).on("click","li.nav-item > a",function(e){
            e.preventDefault();
            let link = $(this).data("nav");
            let active = $(this).parent("li");

            $("li.active").removeClass("active");
            active.addClass("active");

            $.ajax({
                method: "GET",
                url: "/pages/"+link+".php",
                beforeSend: function(){
                    $("#vb-show-content").html(loader);
                },
                success: function(data){
                    setTimeout(function(){
                        $("#vb-show-content").html(data);
                    },500);                    
                }
            });

    });

});