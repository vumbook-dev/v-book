jQuery(document).ready(function($){
    // VARIABLES
    const bookIndex = $("h1#vb-full-title").data("book");  
    const bookFile = $("input#vb-ttl-cdidtfyr").data("universal");
    const bookTemplate = $("h1#vb-full-title").data("template");   

    /*** AUDIO SOUND SCRIPT ***/
    //PROCESS AUDIO
    const newAudio = function(data){
        let json = JSON.parse(data);
        let placeHolder = $("#vbSelectSounds>h4");
        let mediaPlayer = $("#vbMediaPlayerWrap");
        let path = json.filepath;
        let id = json.id;
        let alias = (json.alias.length < 11) ? json.alias : json.alias.substr(0,11);
        let filename = json.filename;
        let sound = '<span id="vb-my-audio" class="slct-sounds-list act-sound h5" data-id="'+id+'">'+alias+' <i class="fa fa-play" aria-hidden="true" data-dir="1" data-path="'+path+'" data-file="'+filename+'"></i></span>';
        $("#vbMyAudioWrap").html(sound);
        mediaPlayer.removeClass("d-none");
        placeHolder.addClass("d-none");
        return true;
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
            beforeSend: function(){
                $('button#vb-sound-upload').html("<i class='bx bx-loader-circle bx-spin' ></i> Saving...");
            },
            success: function(data){             
                window.flashMessage("Audio Successfully Uploaded","success");
                $('button#vb-sound-upload').html("Saved");             
                if(newAudio(data)){                          
                    UpdateSound();
                    setTimeout(function(){
                        $("form#submit-audio").addClass("input-empty");
                        $("#vbSelectSounds input.rdnly-plchldr").addClass("d-none");
                        $("form#submit-audio button.btn-primary").addClass("d-none");
                        $("#vbSelectSounds span.fileUpload").removeClass("d-none");
                        $("div.delay-wrap").removeClass("d-none");
                        $('button#vb-sound-upload').html("Submit");  
                    },1500);
                }
            }  
        }); 
    });

    //PLAY SOUND
    $(document).on("click",".slct-sounds-list > i", function(e){

        let File = $(this).data("file");
        let dir = $(this).data("dir");
        let user = $(this).data("path");
        let path = (dir == 1) ? "users/"+user+"/" : "";
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
        $("span.fileUpload").addClass('d-none');
        $("span.save_changes").removeClass('d-none');
        Sound.volume = vol;
    });

    $(document).on('change','input[name=delay]',function(){
        $("span.fileUpload").addClass('d-none');
        $("span.save_changes").removeClass('d-none');
    });

    $(document).on('click','span.save_changes',function(){
        UpdateSound();
        window.flashMessage('Sound Settings Updated','success');
        $("span.fileUpload").removeClass('d-none');
        $("span.save_changes").addClass('d-none');
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
            window.flashMessage('Please Upload mp3, m4a or wav format only','danger');
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

    //UPDATE SOUND
    const UpdateSound = function(){
        $("div.ql-editor").append(" ");
        let key = $("#vb-save-styles").data("key");
        let parent = $("#vb-save-styles").parents('div.modal-content');
        let sound = parent.find("span#vb-my-audio.act-sound").data("id");
        let delay = parent.find('input[name=delay]').val();
        let volume = $("input[name=vb-volume-control]").val();
        let chapter = $("input[name=chapter]").val();  
        setTimeout(function(){            
            $.ajax({
                method: "POST",
                url: "../model/content.php",
                data: {sound:sound,volume:volume,delay:delay,key:key,file:bookFile,book:bookIndex,chapter:chapter,template:bookTemplate,action:"update_sound"},
                dataType: "text",
                success: function(data){
                    console.log(data);
                    let json = JSON.parse(data);
                    if(json.status == "success"){
                        return true;                        
                    }else{
                        return false;
                    }                    
                }
            });        
        },500);
    }

    const UpdateColor = function(color,key,part){
        $("div.ql-editor").append(" ");
        setTimeout(function(){            
            $.ajax({
                method: "POST",
                url: "../model/content.php",
                data: {color:color,key:key,file:bookFile,book:bookIndex,chapter:part,template:bookTemplate,action:"update_color"},
                dataType: "text",
                beforeSend: function(){
                    $('div.colorPick-wrap input.pcr-save').val('Saving...');
                },
                success: function(data){
                    let json = JSON.parse(data);
                    let state = (json.status == "success") ? "success" : "danger";
                    $('div.colorPick-wrap input.pcr-save').val('Saved');
                    setTimeout(function(){
                        $('div.colorPick-wrap input.pcr-save').val('Save');                        
                        $('div.colorPick-wrap input.pcr-save').removeClass('pckrbtn');
                    },1500);
                    window.flashMessage(json.message,state);                    
                    $("li.apllySoundsToAll").remove();
                }
            });        
        },500);
    }

    //UPDATE COLOR BUTTON
    window.saveBG = function(){
        let key = $("#vb-save-styles").data("key");
        let color;
        let part = $("input[name=chapter]").val();
        let inputColor = $("div.bgContainer>div.custom-radio input#bgColor:checked");
        if(inputColor.length > 0){
            color = $("div.colorPick-wrap input.pcr-result").val();
        }            
        UpdateColor(color,key,part);
    }

    //UPDATE BOOK TEMPLATE CONTENT
    const UpdateContentArray = function(chapter,key,action){
        let allList = $('li.btmp-pages');
        let id = $('ul.btmp-pagelist>li.btmp-pages:nth-child('+allList.length+')').data('pageid');
        let text = (action == 'btmp_update') ? $('div#style-preview>div.ql-editor').html() : "";
        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {chapter:chapter,key:key,id:id,file:bookFile,content:text,action:action},
            dataType: "text",
            success: function(data){
                if(action == 'btmp_add'){
                    let key = Number(data)-1;
                    $("ul.btmp-pagelist").append('<li class="btmp-pages" data-pageid="'+data+'" data-key="'+key+'">'+data+'</li>');
                    $("li#btmp-add-page-btn").appendTo("ul.btmp-pagelist");
                    $("div.btmp-page>div.ql-snow").append('<div class="ql-editor btmp-content btmpPage'+key+'" data-key="'+key+'"></div>');    
                    if(!$("ul.btmp-pagelist>li").hasClass('btmp-active')){
                        $('div.bmtp-startpage').addClass('d-none');   
                        $('div#bookTemplateEditorWrap, div#btmp-action-control-wrap, div#btmpZoomControl').removeClass('d-none');  
                        $('ul.btmp-pagelist>li.btmp-pages:first-child').addClass('btmp-active');
                    }
                }else if(action == 'btmp_delete'){
                    $('ul.btmp-pagelist>li[data-key='+key+']').remove();
                    $('ul.btmp-pagelist>li').removeClass('btmp-active');
                    $('ul.btmp-pagelist>li.btmp-pages:first-child').addClass('btmp-active');
                    let dk = $('li.btmp-active');                    
                    $('div.btmp-content[data-key='+key+']').remove();
                    $('div.btmp-page div.btmp-content:first-child').removeClass('d-none');
                    let newallList = $('li.btmp-pages');
                    //console.log(newallList.length);
                    for(i=0;i<newallList.length;i++){
                        $(newallList[i]).attr('data-key',i);
                    }
                    $('span.btmp-ed-action').attr('data-key',dk.data('key'));
                    $('span.btmp-ed-action>span').text(dk.text());
                    //allList.forEach(element => console.log(element));
                    if(!$("ul.btmp-pagelist>li").hasClass('btmp-active')){
                        $('div.bmtp-startpage').removeClass('d-none');   
                        $('div#bookTemplateEditorWrap, div#btmp-action-control-wrap, div#btmpZoomControl').addClass('d-none');  
                    }
                }else if(action == 'btmp_update'){
                    setTimeout(function(){
                        $('span.btmp-save-wrap').html("<i class='bx bx bx-save' ></i> Saved");
                        setTimeout(function(){
                            $('div.btmpPage'+key).html(text);
                            $("div.btmp-page").toggleClass('d-none');
                            $("div.btmp-editor-wrap>div:first-child").toggleClass('d-none');
                            $('span.btmp-ed-action').toggleClass('d-none');
                            $('span.btmp-save-wrap, span.btmp-cancel-wrap').toggleClass('d-none');
                            $('span.btmp-save-wrap').html("<i class='bx bx bx-save' ></i> Save");
                        },1000);
                    },1500);
                }
            }
        })
    }

    //START A NEW PAGE 
    $(document).on('click','div.bmtp-startpage>span.btn, li#btmp-add-page-btn',function(){
        let chapter = $("#vb-save-styles").data('key');
        UpdateContentArray(chapter,"",'btmp_add');
    });

    //NAVIGATE PAGES
    $(document).on('click','ul.btmp-pagelist>li',function(){        
        if($(this).hasClass('btmp-pages')){
            let key = $(this).data('key');
            let number = $(this).text();
            $('ul.btmp-pagelist>li').removeClass('btmp-active');
            $(this).addClass('btmp-active');
            $('div.btmp-content').addClass('d-none');
            $('div.btmpPage'+key).removeClass('d-none');
            $('span.btmp-ed-action, span.btmp-save-wrap').attr('data-key',key);
            $('span.btmp-ed-action>span').text(number);
        }
    });

    //EDIT CONTENT
    $(document).on('click','div#btmp-action-control-wrap>span.btn-primary.btmp-ed-action, span.btmp-cancel-wrap',function(){
        let key = $('li.btmp-active').data('key');
        let content = $('div.btmpPage'+key).html();
        $('div#style-preview>div.ql-editor').html(content);
        $("div.btmp-page").toggleClass('d-none');
        $("div.btmp-editor-wrap>div:first-child").toggleClass('d-none');
        $('span.btmp-ed-action').toggleClass('d-none');
        $('span.btmp-save-wrap, span.btmp-cancel-wrap').toggleClass('d-none');
    });

    //DELETE CONTENT
    $(document).on('click','span.btn-danger.btmp-ed-action',function(){
        let key = $('li.btmp-active').data('key');
        let chapter = $("#vb-save-styles").data('key');
        UpdateContentArray(chapter,key,'btmp_delete');
    });

    //UPDATE CONTENT
    $(document).on('click','span.btmp-save-wrap',function(){
        let key = $('li.btmp-active').data('key');
        let chapter = $("#vb-save-styles").data('key'); 
        let images;
        $('span.btmp-save-wrap').html("<i class='bx bx-loader-circle bx-spin' ></i> Saving...");    
        images = $("div#style-preview div.ql-editor img");   
        if(images.length > 0 && uploadQuillImage(bookFile)){            
            setTimeout(function(){
                UpdateContentArray(chapter,key,'btmp_update');
            },2500);
        }else{
            UpdateContentArray(chapter,key,'btmp_update');
        }                 
    });

    /*** BACKGROUND  SCRIPT ***/
    $(document).on("click","div.bgContainer > .custom-radio",function(){
        let radio = $(this).data("act");
        let input = $(this).find("input[type=radio]");
        let bgColor = $("div.colorPick-wrap input.pcr-result").val();
        let divColor = $("div.colorPick-wrap");
        let img = $("div.imgPick-wrap");
        let bgIMG = $("img#prev-img-background").attr("src");
        if(radio == 2){
            $("div.bgContainer > .custom-radio:nth-child(2) > input").prop('checked', false);
            divColor.addClass("d-none");
            img.removeClass("d-none");
            input.prop('checked', true);
            if(!$("img#prev-img-background").hasClass('prev-chapter-bg')){
                $("div#style-preview div.ql-editor, div.ql-editor.btmp-content").css("background-image","url('"+bgIMG+"')");
            }else{
                $("div#style-preview").css("background-image","url("+bgIMG+")");
            }
        }else{            
            input.prop('checked', true);
            $("div.bgContainer > .custom-radio:nth-child(3) > input").prop('checked', false);
            if(!$("img#prev-img-background").hasClass('prev-chapter-bg')){
                $("div#style-preview div.ql-editor, div.ql-editor.btmp-content").css("background",bgColor);
            }else{
                $("div#style-preview").css("background",bgColor);
            }
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
                if(!$("#prev-img-background").hasClass('prev-chapter-bg')){
                    $("div#style-preview div.ql-editor, div.ql-editor.btmp-content").css("background-image","url("+e.target.result+")");
                }else{
                    $("div#style-preview").css("background-image","url("+e.target.result+")");
                }                           
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
            window.flashMessage('Please Upload jpg, png or gif format only','danger');
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
            beforeSend: function(){
                $('#submit-background button.btn-primary').html("<i class='bx bx-loader-circle bx-spin' ></i> Saving...");
            },
            success: function(){          
                $('#submit-background button.btn-primary').html("Saved");     
                window.flashMessage('Background Image Successfully Saved.','success');                                 
                uploader.removeClass("d-none");
                uploader.addClass("d-block");
                readOnly.addClass("d-none");
                $("span#rm-image-background").css("display","none");
                submit.addClass("d-none");
                setTimeout(function(data){
                    $("input#upbackground").attr("data-image",data);     
                    $('#submit-background button.btn-primary').html("Save");                 
                },3000);
            }  
        });
    });

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
    window.uploadQuillImage = function(directory){
        let image = $("div#style-preview div.ql-editor img");
        let allImageCount = image.length;        
        let file;
        if(allImageCount > 0){
            for(let i=0; i <= allImageCount; i++){
                if(i != allImageCount){
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
                }else{
                    return true;
                }                               
            }   
            return true;                           
        }else{
            return false;
        }    
    }

    //Cloase Modal
    $(document).on("click","#vb-modal-container .close, #vb-modal-container .btn-secondary",function(){
        $("#vb-modal-container>div").remove();
    });

});