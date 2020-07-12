jQuery(document).ready(function($){

    //SUBMIT MULTI SOUND
    $(document).on("submit","#submit-audio",function(e){
        e.preventDefault();
        window.selfsubmit = $.ajax({  
            url: "../../model/media.php",  
            type: "POST",  
            data: new FormData(this),  
            contentType: false,  
            processData:false,  
            success: function(){                
                $("div.style-widgets-corner").prepend('<div class="message-status alert alert-success" role="alert">Audio Successfully Uploaded</div>');
                setTimeout(function(){
                    $("div.message-status").remove();
                },4000);
                $("form#submit-audio").addClass("input-empty");
                $("input.rdnly-plchldr").addClass("d-none");
                $("form#submit-audio button.btn-primary").addClass("d-none");
                $("span.fileUpload").removeClass("d-none");
                //$(this).removeEventListener();
                loadMyAudio();
                selfsubmit.clear();                      
            }  
        }); 
    });

    //SELECT AUDIO
    $(document).on("click",".slct-sounds > li.slct-sounds-list", function(){
        SoundStatus = $(this).hasClass("act-sound");
        id = $(this).data("id");
        $("li.apllySoundsToAll").remove();
        if(!SoundStatus){
            $(".slct-sounds > li").removeClass("act-sound");            
            $(this).addClass("act-sound");
            $(this).after("<li class='apllySoundsToAll bg-light'><input type='checkbox' data='"+id+"' class='allSounds' name='allSounds'><label class='m-0 px-2'>Set as default sound to all pages?</label></li>");
        }    
        
        //$(this).removeEventListener('click', e);           
    });

    //PLAY SOUND
    $(document).on("click",".slct-sounds > li > i", function(e){
        const PlayStop = function(Sound,drtn){
            Sound.play();           
            setTimeout(function(){
                $(".slct-sounds > li > i").removeClass("fa-pause");
                $(".slct-sounds > li > i").addClass("fa-play");
                Sound.pause();                
            },drtn);
            console.log(drtn);
        }
        let File = $(this).data("file");
        let dir = $(this).data("dir");
        let path = (dir == 1) ? "user/" : "";
        let Sound = new Audio('../../media/sounds/'+path+File);        
        if($(this).hasClass("fa-pause")){
            $(this).find("i").removeClass("fa-pause");
            $(this).find("i").addClass("fa-play");
            Sound.pause();
        }else{            
            let drtn = 2500;
            $(".slct-sounds > li i").removeClass("fa-pause");
            $(".slct-sounds > li i").addClass("fa-play");
            $(this).removeClass("fa-play");
            $(this).addClass("fa-pause");
            PlayStop(Sound,drtn);            
        }
        //$(this).removeEventListener('click', e);
    });

    //CHANGE ALIGNMENT
    $(document).on("click",".vb-text-alignment > li",function(){
        $("div.setDefaultAlignment").remove();
        let act = $(".vb-text-alignment > li.act-align").attr("data");
        let alignTxt = $(this).attr("data");        
        let element = $(".modal-body > .row > div.text-"+act);
        $("ul.vb-text-alignment").after("<div class='setDefaultAlignment py-2'><input type='checkbox' data='"+alignTxt+"' class='allAlignment' name='allAlignment'><label class='m-0 px-2'>Set this as default alignment to all pages?</label></div>");
        element.removeClass("text-"+act);
        element.addClass("text-"+alignTxt);        
        $(".vb-text-alignment > li").removeClass("act-align btn");
        $(this).addClass("act-align btn");
    });

});