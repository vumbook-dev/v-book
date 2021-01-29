jQuery(document).ready(function($){
    //Get Book Template Value
    const bookTemplate = $("h1#vb-full-title").data("template");

    //ADD CONTENT IN DATABASE
    addContent = function(id,name,chapter,book,bookIndex){
        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {id:id,index:bookIndex,name:name,chapter:chapter,title:book,template:bookTemplate,action:"add"},
            dataType: "text",
            success: function(data){
                let json = JSON.parse(data);
                let type = json.errorType;
                //console.log(json.mode,type);
                if(json.mode === "debug_enable" && type === "danger"){
                    failSafeMessage(type,json.errorMSG,json.data);
                }else{
                    if(type === "success"){       
                        loadChapterPart(chapter);                 
                        window.flashMessage(name+' Successfully Added','success');
                    }else{
                        failSafeMessage('secondary',json.errorMSG);
                    }                    
                }                
            }
        })
    }

    //SUBMIT SECTION TITLE
    $(document).on('submit','.vb-new-section',function(e){
        e.preventDefault();
        let selector = $(this).parents("div.card-body");
        let parent = $(this).parents(".tc-wrap");
        let input = parent.find("input.content-name");
        let content = input.val();
        let id = parent.find("input[type=hidden]").val();
        let key = parent.find("button.vb-new-content").data("key");
        let title = parent.find("input[type=hidden]").data("title");
        let bookIndex = parent.find("input[type=hidden]").data("bookindex");
        parent.addClass('d-none');
        selector.find('li.d-none').removeClass('d-none');

        if(content.length != 0){
            addContent(id,content,key,title,bookIndex);
        }        

        input.val("");
    });

    //REMOVE LIGHTBOX
    $(document).on("click","#vb-modal-editor button.close, #vb-modal-preview button.close, #vb-modal-section button.close",function(){
        $("#vb-modal-editor, .modal-backdrop, #vb-modal-preview, #vb-modal-section").remove();
    });

    //ADD CONTENT TO CHAPTER PART
    $(document).on("click","#vb-submit-content",function(){
        let text = $(".ck-editor__main div.ck-editor__editable").html();
        let key = $(this).data("key");
        let chapter = $(this).data("chapter");
        let file = $(this).data("file");
        let title = $(this).parents("#vb-modal-editor").find("h5.modal-title").text();
        let bookCover = $("h1#vb-full-title").data("cover");
        let cover = (bookCover.length != 0) ? bookCover : null;

        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {text:text,key:key,file:file,action:"update"},
            dataType: "text",
            success: function(data){
                setTimeout(function(){
                    loadEditStyle(chapter,key,title,file,cover);
                    //$("#vb-modal-editor, .modal-backdrop").remove();
                    loadChapterPart(data);
                    //$("#btn-content"+key).html('<i class="fa fa-pencil text-muted" data-status="1" aria-hidden="true"></i>');
                },1500);
            }
        })
    });
    
    //LOAD EDITOR
    const loadEditor = function(bookKey,chapter,content,title,lctn){        
        $.ajax({
            method:"POST",
            url:"../pages/parts/"+bookTemplate+"-editor.php",
            data: {bookKey:bookKey,chapter:chapter,content:content,title:title,file:lctn},
            dataType: "text",
            success: function(data){
                $("#vb-modal-container").html(data);
            }
        });
    }

    //EDIT BOOK SECTION
    $(document).on("click",".list-item-vbcontent span.showing-lightbox",function(){
        //let bookCover = $("h1#vb-full-title").data("cover");
        let bookKey = $("h1#vb-full-title").data("book");
        //let cover = (bookCover.length != 0) ? bookCover : null;
        let file = $("#vb-ttl-cdidtfyr").data("universal");
        let chapter = $(this).data("chapter");
        let key = $(this).data("key");
        let title = $(this).parents("li.list-item-vbcontent").find("span.vb-cnt-title").text();
        loadEditor(bookKey,chapter,key,title,file);
    });

    //SHOW ADD SECTION FIELD
    $(document).on('click','span.show-section-field',function(){
        let selector = $(this).parents('div.card-body');
        let field = selector.find('div.tc-wrap');
        $(this).parent('li').addClass('d-none');
        field.removeClass('d-none');
    });

    //GET MODAL CHAPTER DELETE
    function deleteChContent(chapter,content,title){
        $.ajax({
            method:"POST",
            url:"../pages/parts/modal.php",
            data: {chapter:chapter,content:content,title:title,action:"content_delete"},
            dataType: "text",
            success: function(data){                
                $("#vb-modal-container").html(data);               
                $("div.modal").addClass('d-block'); 
                $("div.modal-backdrop").removeClass('d-none');
            }            
        });
        //console.log(chapter + content + title);
    }

    //DELETE CHAPTER
    $(document).on("click","span.vb-dlt-content",function(){        
        let content = $(this).data("key");
        let title = $(this).parents("li.list-item-vbcontent").find("span.vb-cnt-title").text();
        let chapter = $(this).data("chapter");
        deleteChContent(chapter,content,title);        
    });

    //DELETE BOOK CONTENT
    function deleteContent(key,lctn){
        let modal = $("#vb-delete-modal"); 
        let chapter = modal.data('chapter');
        let title = modal.find("span#vb-title-handler").text();
        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {key:key,lctn:lctn,action:"delete"},
            dataType: "text",
            beforeSend: function(){
            modal.find(".modal-body>p").html(`Deleting ... `+title);
            },
            success: function(data){
                let json = JSON.parse(data);
                let type = json.errorType;
                //console.log(json.mode,type);
                if(json.mode === "debug_enable" && type === "danger"){
                    failSafeMessage(type,json.errorMSG,json.data);
                }else{
                    if(type === "success"){              
                        setTimeout(function(){
                            modal.find(".modal-body>p").html(json.flashMSG);            
                            setTimeout(function(){           
                                loadChapterPart(chapter);                        
                                $("#vb-modal-container>div").remove();          
                            },1000);   
                        },1500);
                    }else{
                        failSafeMessage('secondary',json.flashMSG);
                    }                    
                }                        
            }
        });
    }
    
    $(document).on('click','#content_deletevb-confirm-delete',function(){
        let x = $(this).data("key");
        let lctn = $("#vb-ttl-cdidtfyr").data("universal");
        deleteContent(x,lctn);
    });

});