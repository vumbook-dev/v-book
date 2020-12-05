jQuery(document).ready(function($){
    const bookKey = $("h1#vb-full-title").data("book");   
    const bookData = $("input#vb-ttl-cdidtfyr").data("universal");  
    const bookTemplate = $("h1#vb-full-title").data("template");   

    /*** START BOOK CHAPTER ***/
    //LIST BOOK CHAPTER
    window.listBookChapters = function(key){
        $.ajax({
            method: "POST",
            url: "../pages/parts/chapter-list.php",
            data: {key:key,data:bookData},
            dataType: "text",
            success: function(data){
                $("div.chapter-list").html(data);
                loadContent();
            }
        });
    }
    listBookChapters(bookKey);

    //CREATE ADD BOOK CHAPTER
    function addBookChapter(chapter,input){
        $.ajax({
            method: "POST",
            url: "../model/chapters.php",
            data: {key:bookKey,chapter:chapter,file:bookData,action:"add"},
            dataType: "text",
            success: function(data){                
                listBookChapters(bookKey);
                let chptr = JSON.parse(data);
                //addSectionLightbox(chptr['title'],chptr['file'],chptr['chapter'],chptr['index']);
                input.val("");
                $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert"><i class="fa fa-check-circle-o" aria-hidden="true"></i>'+chptr['title']+' Successfully Added</div>');
                setTimeout(function(){
                    $("div.message-status").remove();
                },4000);
            }
        });
    }

    //SUBMIT BOOK CHAPTER
    $(document).on('submit','.bc-wrap form#submit-chapter',function(e){
        e.preventDefault();
        let input = $("#chapter-name");
        let chapter = input.val();
        //$(this).off(e);
        addBookChapter(chapter,input);        
        setTimeout(function(){
            //$("form").reset();
        },500);
    });

    //LOAD CHAPTER PART
    window.loadChapterPart = function(chapter,lightbox = 0){
        let wrap = $("span.vb-chapter"+chapter);
        let title = wrap.find("input[type=hidden]").data("title");
        let id = wrap.find("input[type=hidden]").val();
        $.ajax({
            method: "POST",
            url: "../pages/parts/content-list.php",
            data: {chapter:chapter,title:title,template:bookTemplate,file:bookData,id:id},
            dataType: "text",
            success: function(data){
                $("div#vbcontent-list"+chapter).html(data);
                if(lightbox !== 0){
                    $("div.vbcontent-lightbox").html(data);
                }
                //console.log(title);
            }
        });
    }

    //DELETE CHAPTER
    $(document).on("click","i.vb-chapter-dlt",function(){
        let book = $("#vb-full-title").data("book");
        let chapter = $(this).data("chapter");
        let title = $(this).parents("li.list-item-vbtitle").find("h6").text();
        $.ajax({
            method: "POST",
            url: "../pages/parts/modal.php",
            data: {chapter:chapter,book:book,title:title,file:bookData,action:"chapter_delete"},
            dataType: "text",
            success: function(data){
                $("#vb-modal-container").html(data);                
                //console.log("Delete Chapter");
                //console.log(title);
            }
        });
    });

    //SHOW CHAPTER PARTS
    const loadContent = function(){
        setTimeout(function(){
        let chapters = $("button.btn-chapter");
        let content;
        let count = chapters.length;
        for(i=0;count>i;i++){
            content = $("div#vbcontent-list"+i).html();
            if(content.length === 0){
                loadChapterPart(i);
            }
        }
        },500);
    }
    loadContent();
    // $(document).on("click","button.btn-chapter",function(){
    //     let chapter = $(this).data("chapter");
    //     let content = $("div#vbcontent-list"+chapter).html();
    //     if(content.length === 0){
    //         loadChapterPart(chapter);
    //     }
    //     //console.log(content.length);
    // });

    //GET MODAL CHAPTER DELETE
    function deleteChContent(chapter,content,title){
        $.ajax({
            method:"POST",
            url:"../pages/parts/modal.php",
            data: {chapter:chapter,content:content,title:title},
            dataType: "text",
            success: function(data){                
                $("#vb-modal-container").html(data);                
            }            
        });
        //console.log(chapter + content + title);
    }

    //DELETE CHAPTER PART
    $(document).on("click","span.vb-dlt-content",function(){        
        let content = $(this).data("key");
        let title = $(this).parents("li.list-item-vbcontent").find("span.vb-cnt-title").text();
        let chapter = $(this).data("chapter");
        deleteChContent(chapter,content,title);        
    });

    //PREVIEW CHAPTER PART
    // function previewPart(chapter,content,title,lctn){
    //     $.ajax({
    //         method:"POST",
    //         url:"../pages/parts/content-part-preview.php",
    //         data: {chapter:chapter,content:content,title:title,file:lctn},
    //         dataType: "text",
    //         success: function(data){
    //             $("#vb-modal-container").html(data);
    //         }
    //     });
    // }

    //PREVIEW CHAPTER PART
    // $(document).on("click",".vb-view-content, .back-to-preview",function(){
    //     let content = $(this).data("key");
    //     let title = $(this).data("title");
    //     let chapter = $(this).data("chapter");
    //     let lctn = $("#vb-ttl-cdidtfyr").data("universal");
    //     previewPart(chapter,content,title,lctn);
    // });

    /*** END BOOK CHAPTER ***/

    /*** BOOK COVER SCRIPT ***/
    //PREVIEW BOOK COVER
    function readURL(input,type) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            
            reader.onload = function(e) {
                $('#prev-img-book'+type).attr('src', e.target.result);                
                $('#prev-img-book'+type).removeClass('d-none');
                $("div#book-"+type+"-preview-wrap > i").addClass("d-none");
                $("#upload").text("Update");
                //$('#submit-book-cover input[type=hidden]').val(bookIndex);            
            }
            
            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
    
    //BOOK COVER UPLOADS
    $(document).on('change','#vbUploadBookCover .up, #vbUploadBookBackground .up', function(){
        let names = [];
        let length = $(this).get(0).files.length;
        let parent = $(this).parents('form');
        let type = $(this).data("type");
        let uploader = $(this).parents("span.fileUpload");
        let submit = parent.find("button.btn-primary");        
        let fileExtension = ['jpg', 'png', 'gif'];
        let div = (type == "cover") ? "#vbUploadBookCover" : "#vbUploadBookBackground";
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $(div).prepend('<div class="message-status alert alert-danger" role="alert">Please Upload jpg, png or gif format only</div>');
            setTimeout(function(){
                $("div.message-status").remove();
            },4000);
        }else{
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push($(this).get(0).files[i].name);
            }
            if(length == 1){                
                $(this).closest('.form-group').find('.form-control').attr("value",names);
                readURL(this,type);                
            }

            uploader.addClass("d-none");
            uploader.removeClass("d-block");
            submit.removeClass("d-none");            
            //$("form.input-empty").removeClass("input-empty");
            $(div+" input.rdnly-plchldr").removeClass("d-none");

            $(this).unbind();
        }
            
    });
    
    //SUBMIT BOOK COVER
    $(document).on("submit","#submit-book-cover, #submit-book-background",function(e){
        e.preventDefault();
        let img = $("#prev-img-bookcover");
        let formData = new FormData(this);
        let json;
        //formData.append( 'file', $( '#upcover' )[0].files[0] );
        //formData.append("book",bookIndex);
        window.selfsubmit = $.ajax({  
            url: "../model/books.php",  
            type: "POST",  
            data: formData,  
            contentType: false,  
            processData:false,  
            success: function(data){   
                json = JSON.parse(data);            
                $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Book '+json.message+' Successfully Uploaded</div>');
                setTimeout(function(){
                    $("div.message-status").remove();
                },4000);
                $("form#submit-book-"+json.type).addClass("input-empty");
                $("#vbUploadBook"+json.message+" input.rdnly-plchldr").addClass("d-none");
                $("form#submit-book-"+json.type+" button.btn-primary").addClass("d-none");
                $("#vbUploadBook"+json.message+" span.fileUpload").removeClass("d-none");
                console.log(json.type,json.message);                            
            }  
        }); 
    });
    
    /*** BOOK COVER SCRIPT END ***/

    //LOAD CHAPTER EDITOR
    const loadChEditor = function(chapter){
        $.ajax({
            method:"POST",
            url:"../pages/parts/chapter-editor.php",
            data: {book:bookKey,file:bookData,chapter:chapter},
            dataType: "text",
            success: function(data){
                $("#vb-modal-container").html(data);
            }
        });
    }

    //EDIT CHAPTER
    $(document).on('click','.ch-editor',function(e){
        e.preventDefault();
        let chapter = $(this).data("ch");
        loadChEditor(chapter);
    });

    //UPDATE CHAPTER STYLE
    const updateChPage = function(chapter,sound,volume,delay,color,title,subtitle) {
        let content = $("div.ql-editor").html();
        $.ajax({
            method: "POST",
            url: "/model/chapters.php",
            data: {book:bookKey,file:bookData,chapter:chapter,sound:sound,volume:volume,delay:delay,color:color,title:title,subtitle:subtitle,content:content,action:"update"},
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

    $(document).on("click","#vb-save-chPage",function(){
        let key = $(this).data("key");
        let parent = $(this).parents('div.modal-content');
        let title = $("h1.ch-main-title").text();
        let subtitle = $("p.ch-subtitle").text();
        //let file = $("input#vb-ttl-cdidtfyr").data("universal");
        let actSound = $("span.act-sound").data("id");
        let vol = $("input[name=vb-volume-control]").val();
        let delay = parent.find('input[name=delay]').val();
        let color;
        let inputColor = $("div.bgContainer>div.custom-radio input#bgColor:checked");
        if(inputColor.length > 0){
            color = $("div.colorPick-wrap input.pcr-result").val();
        }
        if(uploadQuillImage(bookData)){
            updateChPage(key,actSound,vol,delay,color,title,subtitle);
            console.log(1);
        }else{
            updateChPage(key,actSound,vol,delay,color,title,subtitle);
            console.log(2);
        }        
    });

    //ENABLE EDITABLE BOOK TITLE
    $(document).on('click','span.editable-title',function(){
        let title = $('h1#vb-full-title');
        let titleText = title.html();
        let editView = titleText.replace('<small class="d-block h6">','{').replace('</small>','}').replace('<span class="editable-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>','');
        $('label.editable-label-title').addClass('d-block');
        $('#vb-show-content>div:nth-child(2) > div.editable-btn').addClass('d-block');
        title.attr("original_entry",title.html());
        title.html(editView);
        title.attr("contenteditable","true");
        title.addClass('h1-editable');
    });

    //CANCEL EDIT BOOK TITLE
    $(document).on('click','div.editable-btn>button.btn-danger',function(e){
        e.preventDefault();
        let title = $('h1#vb-full-title');
        let originalText = title.attr('original_entry');        
        $('label.editable-label-title').removeClass('d-block');
        $('div.editable-btn').removeClass('d-block');
        title.html(originalText);
        title.attr("contenteditable","false");
        title.removeClass('h1-editable');
        $('span.editable-title').removeClass('d-none');
    });

    //ENABLE EDITABLE BOOK CHAPTER TITLE
    $(document).on('click','span.editable-chtitle',function(){
        let parent = $(this).parents('li.list-item-vbtitle');
        let title = parent.find('h6');
        let titleText = title.html();
        let editView = titleText.replace('<small class="vb-content-subtitle h6">','{').replace('</small>','}').replace('<span class="editable-chtitle px-2 d-none d-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>','');
        let editorPen = $(this);
        title.attr("original_entry",title.html());
        title.html(editView);
        title.addClass('h6-editable');
        title.attr("contenteditable","true");
        parent.find('div.cheditable-btn').addClass('d-block');
        editorPen.removeClass('d-inline');
        parent.find('label.editable-label-title').addClass('d-block');
    });

    //CANCEL EDIT CHAPTER TITLE
    $(document).on('click','div.cheditable-btn>button.btn-danger',function(e){
        e.preventDefault();
        let parent = $(this).parents('li.list-item-vbtitle');
        let title = parent.find('h6');
        let originalText = title.attr('original_entry');        
        parent.find('label.editable-label-title').removeClass('d-block');
        parent.find('div.cheditable-btn').removeClass('d-block');
        title.html(originalText);
        title.attr("contenteditable","false");
        title.removeClass('h6-editable');
        $('span.cheditable-title').removeClass('d-block');
    });

    //ENABLE EDITABLE BOOK SECTION TITLE
    $(document).on('click','span.editable-section',function(){
        let parent = $(this).parents('li.list-item-vbcontent');
        let title = parent.find('span.vb-cnt-title');
        let titleText = title.html();
        let editView = titleText.replace('<span class="editable-section pr-2 d-none d-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>','');
        let editorPen = $(this);
        title.attr("original_entry",title.html());
        title.html(editView);
        title.addClass('h6-editable');
        title.attr("contenteditable","true");
        parent.find('div.sceditable-btn').addClass('d-block');
        editorPen.removeClass('d-inline');
    });

    //CANCEL EDIT CHAPTER TITLE
    $(document).on('click','div.sceditable-btn>button.btn-danger',function(e){
        e.preventDefault();
        let parent = $(this).parents('li.list-item-vbcontent');
        let title = parent.find('span.vb-cnt-title');
        let originalText = title.attr('original_entry');        
        parent.find('div.sceditable-btn').removeClass('d-block');
        title.html(originalText);
        title.attr("contenteditable","false");
        title.removeClass('h6-editable');
        $('span.sceditable-title').removeClass('d-block');
    });

    const saveBookTitles = function(modelname,newTitle,chapter = null,section = null){
        $.ajax({
            method: "POST",
            url: "/model/"+modelname+".php",
            data: {title:newTitle,book:bookKey,file:bookData,chapter:chapter,section:section,action:"update_title"},
            dataType: "text",            
            success: function(data){
                //console.log(data);
                $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert">'+data+'</div>');
                setTimeout(function(){
                    $("div.message-status").remove();             
                },3000);
            }
        });
        return true;
    }

    //SAVE EDIT BOOK TITLE
    $(document).on('click','div.editable-btn>button.btn-primary',function(e){
        e.preventDefault();
        let title = $('h1#vb-full-title');   
        if(saveBookTitles("books",title.text())){         
            let editButton = '<span class="editable-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>';  
            let newTitle = title.text().replace('{',editButton+'<small class="d-block h6">').replace('}','</small>');
            newTitle = (title == newTitle) ? title+" "+editButton : newTitle;
            $('label.editable-label-title').removeClass('d-block');
            $('div.editable-btn').removeClass('d-block');
            title.html(newTitle);
            title.attr("contenteditable","false");
            title.removeClass('h1-editable');
            $('span.editable-title').removeClass('d-none');    
        }         
    });

    //SAVE EDIT CHAPTER TITLE
    $(document).on('click','div.cheditable-btn>button.btn-primary',function(e){
        e.preventDefault();
        let parent = $(this).parents('li.list-item-vbtitle');
        let title = parent.find('h6');
        let chapter = title.data('ch');
        //let titleText = title.html();
        if(saveBookTitles("chapters",title.text(),chapter)){         
            let editButton = '<span class="editable-chtitle px-2 d-none d-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>';  
            let newTitle = editButton+title.text().replace('{','<small class="vb-content-subtitle h6">').replace('}','</small>');
            newTitle = (title == newTitle) ? editButton+" "+title : newTitle;
            parent.find('label.editable-label-title').removeClass('d-block');
            parent.find('div.cheditable-btn').removeClass('d-block');
            title.html(newTitle);
            title.attr("contenteditable","false");
            title.removeClass('h6-editable');
            $('span.cheditable-title').removeClass('d-block');      
        }         
    });

    //SAVE EDIT CHAPTER TITLE
    $(document).on('click','div.sceditable-btn>button.btn-primary',function(e){
        e.preventDefault();
        let parent = $(this).parents('li.list-item-vbcontent');
        let title = parent.find('span.vb-cnt-title');
        let chapter = title.data('ch');
        let section = title.data('sctn');
        if(saveBookTitles("content",title.text(),chapter,section)){         
            let editButton = '<span class="editable-section pr-2 d-none d-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>';  
            let newTitle = editButton+title.text();
            parent.find('div.sceditable-btn').removeClass('d-block');
            title.html(newTitle);
            title.attr("contenteditable","false");
            title.removeClass('h6-editable');
            $('span.sceditable-title').removeClass('d-block');      
        }         
    });

});