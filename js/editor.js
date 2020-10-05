jQuery(document).ready(function($){
    // VARIABLES
    const bookIndex = $("h1#vb-full-title").data("book");    

    /*** AUDIO SOUND SCRIPT ***/
    //PROCESS AUDIO
    const newAudio = function(data){
        let json = JSON.parse(data);
        let placeHolder = $("#vbSelectSounds>h4");
        let mediaPlayer = $("#vbMediaPlayerWrap");
        let id = json.id;
        let alias = (json.alias.length < 11) ? json.alias : json.alias.substr(0,11);
        let filename = json.filename;
        let sound = '<span id="vb-my-audio" class="slct-sounds-list act-sound h5" data-id="'+id+'">'+alias+' <i class="fa fa-play" aria-hidden="true" data-dir="1" data-file="'+filename+'"></i></span>';
        $("#vbMyAudioWrap").html(sound);
        mediaPlayer.removeClass("d-none");
        placeHolder.addClass("d-none");
    }
    //SUBMIT MULTI SOUND
    $(document).on("submit","#submit-audio",function(e){
        e.preventDefault();
        window.selfsubmit = $.ajax({  
            url: "../../model/media.php",  
            type: "POST",  
            data: new FormData(this),  
            contentType: false,  
            processData:false,  
            success: function(data){                
                $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Audio Successfully Uploaded</div>');
                setTimeout(function(){
                    $("div.message-status").remove();
                },4000);
                $("form#submit-audio").addClass("input-empty");
                $("#vbSelectSounds input.rdnly-plchldr").addClass("d-none");
                $("form#submit-audio button.btn-primary").addClass("d-none");
                $("#vbSelectSounds span.fileUpload").removeClass("d-none");
                //$(this).removeEventListener();
                //loadMyAudio();
                //selfsubmit.clear();               
                newAudio(data);       
            }  
        }); 
    });

    //SELECT AUDIO
    // $(document).on("click",".slct-sounds > li.slct-sounds-list", function(){
    //     SoundStatus = $(this).hasClass("act-sound");
    //     id = $(this).data("id");
    //     $("li.apllySoundsToAll").remove();
    //     if(!SoundStatus){
    //         $(".slct-sounds > li").removeClass("act-sound");            
    //         $(this).addClass("act-sound");
    //         $(this).after("<li class='apllySoundsToAll bg-light'><input type='checkbox' data='"+id+"' class='allSounds' name='allSounds'><label class='m-0 px-2'>Set as default sound to all pages?</label></li>");
    //     }    
        
    //     //$(this).removeEventListener('click', e);           
    // });

    //PLAY SOUND
    $(document).on("click",".slct-sounds-list > i", function(e){

        let File = $(this).data("file");
        let dir = $(this).data("dir");
        let path = (dir == 1) ? "user/" : "";
        let vol = $("input[name=vb-volume-control]").val();
        let Sound = $("#vb-prevAudio")[0];
            Sound.src = '../../media/sounds/'+path+File;   
            Sound.volume = vol;
            Sound.loop = true;
        if($(this).hasClass("fa-pause")){
            $(this).removeClass("fa-pause");
            $(this).addClass("fa-play");
            Sound.pause();
        }else{            
            $(".slct-sounds-list i").removeClass("fa-pause");
            $(".slct-sounds-list i").addClass("fa-play");
            $(this).removeClass("fa-play");
            $(this).addClass("fa-pause");
            Sound.play();           
        }
        
    });

    $(document).on('change','input[name=vb-volume-control]',function(){
        let Sound = $("#vb-prevAudio")[0];
        let vol = $(this).val();
        Sound.volume = vol;
        //console.log(vol);
    });

    //MULTIPLE SOUND UPLOADS
    $(document).on('change','#vbSelectSounds .up', function(){
        let names = [];
        let uploader = $(this).parents("span.fileUpload");
        let submit = $("#vbSelectSounds button.btn-primary");
        let readOnly = $("#vbSelectSounds input.rdnly-plchldr");
        let fileName = $(this).get(0).files[0].name;
        let fileExtension = ['mp3', 'wav', 'm4a'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-danger" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please Upload mp3, m4a or wav format only</div>');
            setTimeout(function(){
                $("div.message-status").remove();
            },4000);
        }else{
            readOnly.attr("value",fileName);
            uploader.addClass("d-none");
            submit.removeClass("d-none");
            $("#vbSelectSounds form.input-empty").removeClass("input-empty");
            readOnly.removeClass("d-none");
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

    //UPDATE STYLE
    const UpdateContent = function(sound,volume,key,file,chapter,color,index = bookIndex){
        $("div.ql-editor").append(" ");
        setTimeout(function(){            
            let content = JSON.stringify(Quillcontents, null, 2);
            $.ajax({
                method: "POST",
                url: "../model/content.php",
                data: {sound:sound,volume:volume,key:key,file:file,book:index,content:content,chapter:chapter,color:color,action:"update"},
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
            });        
        },500);
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
        let parent = $(this).parents("div.imgPick-wrap");
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
            $("img#prev-img-background").removeClass("d-none");
            parent.find("span#rm-image-background").css("display","block");       
               
            //$("form.input-empty").removeClass("input-empty");
            $("#vbIMGbackground input.rdnly-plchldr").removeClass("d-none");

            $(this).unbind();
        }
            
    });

    //REMOVE IMAGE BACKGROUND
    $(document).on('click','span#rm-image-background',function(){
        let uploader = $("span.fileUpload");
        let submit = $("#vbIMGbackground button.btn-primary"); 
        let parent = $(this).parents("#imgBackground-preview-wrap");
        $(this).css("display","none");
        uploader.addClass("d-block");
        uploader.removeClass("d-none");
        submit.addClass("d-none");
        $("input.rdnly-plchldr").addClass("d-none");
        $(parent).find("img").addClass("d-none");
        $(parent).find("i.fa-picture-o").removeClass("d-none");
        $("#upbackground.up").val("");
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
                $("span#rm-image-background").css("display","none");
                submit.addClass("d-none");
                setTimeout(function(data){
                    $("input#upbackground").attr("data-image",data);
                    console.log(data);
                    $("div.message-status").remove();
                },3000);
            }  
        });
    });

    //SAVE COLOR PICK
    // window.saveBG = function(key,type,value){
    //     let file = $("input#vb-ttl-cdidtfyr").data("universal");
    //     $.ajax({
    //         url: "../model/books.php",
    //         type: "POST",
    //         data: {action: "update",key: key, file: file, bgType: type, bgValue: value},
    //         dataType: "text",
    //         success: function(data){
    //             //console.log("Background Saved "+ data);
    //         }
    //     });
    // }

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
                            console.log(rs);                            
                        }
                    });                                          
                }                                 
            }                                
            return true; 
        }else{
            return true;
        }    
    }

    $(document).on("click","#vb-save-styles",function(){
        //console.log(content);
        let key = $(this).data("key");
        let actSound = $("span.act-sound").data("id");
        let file = $("input#vb-ttl-cdidtfyr").data("universal");
        let vol = $("input[name=vb-volume-control]").val();
        //let ApplyAllSounds = $("input.allSounds");
        //let dsounds = (ApplyAllSounds.prop("checked") == true) ? 1 : 0;
        let color;
        let chapter = $("input[name=chapter]").val();
        let inputColor = $("div.bgContainer>div.custom-radio input#bgColor:checked");
        if(inputColor.length > 0){
            color = $("div.colorPick-wrap input.pcr-result").val();
        }             
        if(uploadQuillImage(file)){
            UpdateContent(actSound,vol,key,file,chapter,color);
        }else{
            //alert("No Image");
        } 
    });

});