jQuery(document).ready(function($){
    // VARIABLES
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
                $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Audio Successfully Uploaded</div>');
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

        let File = $(this).data("file");
        let dir = $(this).data("dir");
        let path = (dir == 1) ? "user/" : "";
        let Sound = $("#vb-prevAudio")[0];
            Sound.src = '../../media/sounds/'+path+File;        
        if($(this).hasClass("fa-pause")){
            $(this).removeClass("fa-pause");
            $(this).addClass("fa-play");
            Sound.pause();
        }else{            
            $(".slct-sounds > li i").removeClass("fa-pause");
            $(".slct-sounds > li i").addClass("fa-play");
            $(this).removeClass("fa-play");
            $(this).addClass("fa-pause");
            Sound.play();           
        }
        
    });

    //MULTIPLE SOUND UPLOADS
    $(document).on('change','#vb-sound-upload .up', function(){
        let names = [];
        let length = $(this).get(0).files.length;
        let uploader = $(this).parents("span.fileUpload");
        let submit = $("#vb-sound-upload button.btn-primary");
        let fileExtension = ['mp3', 'wav', 'm4a'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-danger" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please Upload mp3, m4a or wav format only</div>');
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

    
    /*** STYLE SCRIPT ***/

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

    // //CHANGE ALIGNMENT
    // $(document).on("click",".vb-text-alignment > li",function(){
    //     $("div.setDefaultAlignment").remove();
    //     let act = $(".vb-text-alignment > li.act-align").attr("data");
    //     let alignTxt = $(this).attr("data");        
    //     let element = $(".modal-body > .row > div.text-"+act);
    //     $("ul.vb-text-alignment").after("<div class='setDefaultAlignment py-2'><input type='checkbox' data='"+alignTxt+"' class='allAlignment' name='allAlignment'><label class='m-0 px-2'>Set this as default alignment to all pages?</label></div>");
    //     element.removeClass("text-"+act);
    //     element.addClass("text-"+alignTxt);        
    //     $(".vb-text-alignment > li").removeClass("act-align btn");
    //     $(this).addClass("act-align btn");
    // });

    //UPDATE STYLE
    const UpdateStyle = function(sound,dSound,key,file,content,index = bookIndex){
        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {sound:sound,dSound:dSound,key:key,file:file,book:index,content:content,action:"update"},
            dataType: "text",
            success: function(data){
                let json = JSON.parse(data);
                let state = (json.status == "success") ? "alert-success" : "alert-danger";
                $("div#vbUpdateMessage").prepend('<div class="message-status alert '+state+'" role="alert">'+json.message+'</div>');
                //$("#vb-new-section").removeClass("d-none");
                $("li.apllySoundsToAll").remove();
                setTimeout(function(){
                    $("div.message-status").remove();                    
                },3000);
            }
        })
    }

    /*** BACKGROUND  SCRIPT ***/
    $(document).on("click","div.bgContainer > .custom-radio",function(){
        let radio = $(this).data("act");
        let input = $(this).find("input");
        let bgColor = $("div.colorPick-wrap input.pcr-result").val();
        let divColor = $("div.colorPick-wrap");
        let img = $("div.imgPick-wrap");
        let bgIMG = $("img#prev-img-background").attr("src");
        if(radio == 2){
            $("div.bgContainer > .custom-radio:nth-child(2) > input").prop('checked', false);
            divColor.addClass("d-none");
            img.removeClass("d-none");
            input.prop('checked', true);
            $("div#style-preview").css("background","url('"+bgIMG+"')");
        }else{
            input.prop('checked', true);
            $("div.bgContainer > .custom-radio:nth-child(3) > input").prop('checked', false);
            $("div#style-preview").css("background",bgColor);
            img.addClass("d-none");
            divColor.removeClass("d-none");
        }
    });

    //BACKGROUND IMAGE UPLOADS
    function readURL(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            
            reader.onload = function(e) {
                $('#prev-img-background').attr('src', e.target.result);  
                $("div#style-preview").css("background","url("+e.target.result+")");               
                $('#prev-img-background').removeClass('d-none');
                $("div#imgBackground-preview-wrap > i").addClass("d-none");
                $("#upload").text("Update");
                //$('#submit-book-cover input[type=hidden]').val(bookIndex);            
            }
            
            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }

    $(document).on('change','#upbackground.up', function(){
        let names = [];
        let length = $(this).get(0).files.length;
        let uploader = $(this).parents("span.fileUpload");
        let submit = $("#vbIMGbackground button.btn-primary");        
        let fileExtension = ['jpg', 'png', 'gif'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-danger" role="alert">Please Upload jpg, png or gif format only</div>');
            setTimeout(function(){
                $("div.message-status").remove();
            },3000);
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
            $("#vbIMGbackground input.rdnly-plchldr").removeClass("d-none");

            $(this).unbind();
        }
            
    });

    //SAVE IMAGE BACKGROUND
    $(document).on("submit","form#submit-background",function(e){
        e.preventDefault();
        let uploader = $(this).parents("#vbIMGbackground").find("span.fileUpload");
        let submit = $("#vbIMGbackground button.btn-primary");
        let readOnly = $("input.rdnly-plchldr");
        window.selfsubmit = $.ajax({  
            url: "../model/media.php",  
            type: "POST",  
            data: new FormData(this),  
            contentType: false,  
            processData:false,  
            success: function(){                
                $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Background Image Successfully Saved</div>');                     
                uploader.removeClass("d-none");
                uploader.addClass("d-block");
                readOnly.addClass("d-none");
                submit.addClass("d-none");
                setTimeout(function(){
                    $("div.message-status").remove();
                },3000);
            }  
        });
    });

    //SAVE COLOR PICK
    window.saveBG = function(key,type,value){
        $.ajax({
            url: "../model/books.php",
            type: "POST",
            data: {action: "update",key: key, bgType: type, bgValue: value},
            dataType: "text",
            success: function(data){
                //console.log("Background Saved "+ data);
            }
        });
    }

    //CONVERT BASED64 SOURCE TO FILE
    const DataURIToBlob = function(dataURI) {
    const splitDataURI = dataURI.split(',')
    const byteString = splitDataURI[0].indexOf('base64') >= 0 ? atob(splitDataURI[1]) : decodeURI(splitDataURI[1])
    const mimeString = splitDataURI[0].split(':')[1].split(';')[0]

    const ia = new Uint8Array(byteString.length)
    for (let i = 0; i < byteString.length; i++)
        ia[i] = byteString.charCodeAt(i)

    return new Blob([ia], { type: mimeString })
    }

    //PROCESS QUILL IMAGE FORM
    const uploadQuillImage = function(directory){
        let image = $("div.ql-editor img");
        let allImageCount = image.length;        
        let file;
        if(allImageCount > 0){
            for(let i=0; i < allImageCount; i++){
                let dataIMG = new FormData;
                dataIMG.append("dir",directory);
                //let src = image[i].attributes[0].value;
                let src = $(image[i]).attr("src");
                let res = src.split(':');
                if(res[0] == "data"){
                    //console.log("Yes this is from based64");   
                    file = DataURIToBlob(src);                    
                    dataIMG.append("image[]", file);   
                    window.selfsubmit = $.ajax({
                        url: "../model/media.php",
                        method: "POST",
                        contentType: false,  
                        processData:false, 
                        data: dataIMG,
                        success: function(data){
                            let rs = JSON.parse(data);
                            $(image[i]).attr("src",rs.url);
                            //console.log(rs);                            
                        }
                    });                                          
                }                                 
            }  
            $("div.ql-editor").append(" ");                    
            return true; 
        }else{
            return true;
        }    
    }

    $(document).on("click","#vb-save-styles",function(){
        let content = JSON.stringify(Quillcontents, null, 2);
        //console.log(content);
        let key = $(this).data("key");
        let actSound = $("li.act-sound").data("id");
        let file = $("#vb-ttl-cdidtfyr").data("universal");
        let ApplyAllSounds = $("input.allSounds");
        let dsounds = (ApplyAllSounds.prop("checked") == true) ? 1 : 0;  
        if(uploadQuillImage(file)){
            UpdateStyle(actSound,dsounds,key,file,content);
        }
        let book = $("#vb-full-title").data("book");
        let color = $("div.colorPick-wrap input.pcr-result").val();
        let image = $("input#upbackground").val();

        let inputColor = $("div.bgContainer>div.custom-radio input#bgColor:checked");
        //let image = $("div.bgContainer>div.custom-radio input#bgIMG");
        //console.log(inputColor);
        if(inputColor.length > 0){
            saveBG(book,"color",color); 
        }else{
            saveBG(book,"image",image);            
        }                     
    });

});