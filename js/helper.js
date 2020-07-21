jQuery(document).ready(function($){
    // VARIABLES //
    const bookIndex = $("h1#vb-full-title").data("book");    

    /*** AUDIO SOUND SCRIPT ***/
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
                $("#vbSelectSounds input.rdnly-plchldr").addClass("d-none");
                $("form#submit-audio button.btn-primary").addClass("d-none");
                $("#vbSelectSounds span.fileUpload").removeClass("d-none");
                //$(this).removeEventListener();
                loadMyAudio();
                //selfsubmit.clear();                      
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

    //MULTIPLE SOUND UPLOADS
    $(document).on('change','#vb-sound-upload .up', function(){
        let names = [];
        let length = $(this).get(0).files.length;
        let uploader = $(this).parents("span.fileUpload");
        let submit = $("#vb-sound-upload button.btn-primary");
        let fileExtension = ['mp3', 'wav'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $("div.style-widgets-corner").prepend('<div class="message-status alert alert-danger" role="alert">Please Upload mp3 or wav format only</div>');
            setTimeout(function(){
                $("div.message-status").remove();
            },4000);
        }else{
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push($(this).get(0).files[i].name);
            }
            // $("input[name=file]").val(names);
            if(length>1){
                var fileName = names.join(', ');
                $(this).closest('.form-group').find('.form-control').attr("value",length+" files selected");
            }
            else{
                $(this).closest('.form-group').find('.form-control').attr("value",names);
            }

            uploader.addClass("d-none");
            submit.removeClass("d-none");
            $("#vb-sound-upload form.input-empty").removeClass("input-empty");
            $("#vb-sound-upload input.rdnly-plchldr").removeClass("d-none");

            $(this).unbind();
        }
            
    });
    
    //SCROLLBAR
    $(document).on("mouseenter","#style-preview, #default-sounds div.card-body, #personal-sounds div.card-body",function() {
        $(this).addClass("scrollMouseIn");
    })
    .mouseleave(function() {
        $(this).removeClass("scrollMouseIn");
    });
    
    $(document).on("click",".editstyle-close",function(){
        $("#vb-modal-container div").remove();
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

    /*** AUDIO SOUND SCRIPT END ***/

    /*** BOOK COVER SCRIPT ***/
    //PREVIEW BOOK COVER
    function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        
        reader.onload = function(e) {
            $('#prev-img-bookcover').attr('src', e.target.result);
            $('#prev-img-bookcover').removeClass('d-none');
            $('#submit-book-cover input[type=hidden]').val(bookIndex);            
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
    }

    //BOOK COVER UPLOADS
    $(document).on('change','#vbUploadBookCover .up', function(){
        let names = [];
        let length = $(this).get(0).files.length;
        let uploader = $(this).parents("span.fileUpload");
        let submit = $("#vbUploadBookCover button.btn-primary");
        let fileExtension = ['jpg', 'png', 'gif'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $("div#vbUploadBookCover").prepend('<div class="message-status alert alert-danger" role="alert">Please Upload jpg, png or gif format only</div>');
            setTimeout(function(){
                $("div.message-status").remove();
            },4000);
        }else{
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push($(this).get(0).files[i].name);
            }
            if(length == 1){                
                $(this).closest('.form-group').find('.form-control').attr("value",names);
                readURL(this);
            }

            uploader.addClass("d-none");
            uploader.removeClass("d-block");
            submit.removeClass("d-none");
            //$("form.input-empty").removeClass("input-empty");
            $("#vbUploadBookCover input.rdnly-plchldr").removeClass("d-none");

            $(this).unbind();
        }
            
    });

    //SUBMIT BOOK COVER
    $(document).on("submit","#submit-book-cover",function(e){
        e.preventDefault();
        let formData = new FormData(this);
        //formData.append( 'file', $( '#upcover' )[0].files[0] );
        //formData.append("book",bookIndex);
        window.selfsubmit = $.ajax({  
            url: "../model/books.php",  
            type: "POST",  
            data: formData,  
            contentType: false,  
            processData:false,  
            success: function(data){                
                $("div#vbUploadBookCover").prepend('<div class="message-status alert alert-success" role="alert">Book Cover Successfully Uploaded</div>');
                setTimeout(function(){
                    $("div.message-status").remove();
                },4000);
                $("form#submit-book-cover").addClass("input-empty");
                $("#vbUploadBookCover input.rdnly-plchldr").addClass("d-none");
                $("form#submit-book-cover button.btn-primary").addClass("d-none");
                $("#vbUploadBookCover span.fileUpload").removeClass("d-none");
                $("h1#vb-full-title").attr("data-cover",data); 
                //$(this).removeEventListener();
                //selfsubmit.clear();       
                //$(".modal-body").prepend(data);               
            }  
        }); 
    });

    /*** BOOK COVER SCRIPT END ***/

    /*** STYLE SCRIPT ***/
    //UPDATE STYLE
    const UpdateStyle = function(sound,dSound,key,file,bg,align,dAlign,index = bookIndex){
        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {sound:sound,dSound:dSound,key:key,file:file,bg:bg,align:align,dAlign:dAlign,book:index,action:"update"},
            dataType: "text",
            success: function(data){
                let json = JSON.parse(data);
                let state = (json.status == "success") ? "alert-success" : "alert-danger";
                $("div.style-widgets-corner").prepend('<div class="message-status alert '+state+'" role="alert">'+json.message+'</div>');
                //$("#vb-new-section").removeClass("d-none");
                $("li.apllySoundsToAll").remove();
                setTimeout(function(){
                    $("div.message-status").remove();                    
                },3000);
            }
        })
    }

    $(document).on("click","#vb-save-styles",function(){
        let key = $(this).data("key");
        let actSound = $("li.act-sound").data("id");
        let file = $("#vb-ttl-cdidtfyr").data("universal");
        let align = $(".vb-text-alignment > li.act-align").attr("data");
        let ApplyAllSounds = $("input.allSounds");
        let dsounds = (ApplyAllSounds.prop("checked") == true) ? 1 : 0;        
        let ApplyAllAlignment = $("input.allAlignment");
        let dAlign = (ApplyAllAlignment.prop("checked") == true) ? 1 : 0;
        let bg = 0;
        UpdateStyle(actSound,dsounds,key,file,bg,align,dAlign);
        $("div.setDefaultAlignment").remove();
    });

    /*** STYLE SCRIPT END ***/

});