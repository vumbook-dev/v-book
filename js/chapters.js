jQuery(document).ready(function(){
    const bookKey = $("h1#vb-full-title").data("book");

    /*** START BOOK CHAPTER ***/
    //LIST BOOK CHAPTER
    window.listBookChapters = function(key){
        $.ajax({
            method: "POST",
            url: "../pages/parts/chapter-list.php",
            data: {key:key},
            dataType: "text",
            success: function(data){
                $("div.chapter-list").html(data);
            }
        });
    }
    listBookChapters(bookKey);

    //CREATE ADD BOOK CHAPTER
    function addBookChapter(chapter,input){
        $.ajax({
            method: "POST",
            url: "../model/chapters.php",
            data: {key:bookKey,chapter:chapter,action:"add"},
            dataType: "text",
            success: function(data){                
                listBookChapters(bookKey);
                let chptr = JSON.parse(data);
                addSectionLightbox(chptr['title'],chptr['file'],chptr['chapter'],chptr['index']);
                //console.log(chptr['title']);
                input.val("");
            }
        });
    }

    //SUBMIT BOOK CHAPTER
    $(document).on('submit','.bc-wrap form',function(e){
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
            data: {chapter:chapter,title:title,id:id},
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
    $(document).on("click","button.vb-chapter-dlt",function(){
        let book = $("#vb-full-title").data("book");
        let chapter = $(this).data("chapter");
        let title = $(this).parents("li.list-item-vbtitle").find("h6").text();
        $.ajax({
            method: "POST",
            url: "../pages/parts/modal.php",
            data: {chapter:chapter,book:book,title:title,action:"chapter_delete"},
            dataType: "text",
            success: function(data){
                $("#vb-modal-container").html(data);
                console.log("Delete Chapter");
                //console.log(title);
            }
        });
    });

    //SHOW CHAPTER PARTS
    $(document).on("click","button.btn-chapter",function(){
        let chapter = $(this).data("chapter");
        let content = $("div#vbcontent-list"+chapter).html();
        if(content.length === 0){
            loadChapterPart(chapter);
        }
        //console.log(content.length);
    });

    /*** END BOOK CHAPTER ***/

    /*** START BOOK SECTION ***/

    
    //SUBMIT SECTION TITLE
    $(document).on('submit','.vb-new-section',function(e){
        e.preventDefault();
        let parent = $(this).parents(".tc-wrap");
        let input = parent.find("input.content-name");
        let content = input.val();
        let id = parent.find("input[type=hidden]").val();
        let key = parent.find("button.vb-new-content").data("key");
        let title = parent.find("input[type=hidden]").data("title");
        let bookIndex = parent.find("input[type=hidden]").data("bookindex");

        if(content.length != 0){
            addContent(id,content,key,title,"",bookIndex);
        }        

        input.val("");
    });

    //LOAD LIGHTBOX FOR ADDING NEW SECTION
    window.addSectionLightbox = function(title,file,chapter,index){
        $.ajax({
            method: "POST",
            url: "../pages/parts/section-lightbox.php",
            data: {title:title,file:file,chapter:chapter,bookIndex:index},
            dataType: "text",
            success: function(data){
                $("#vb-modal-container").html(data);              
                //console.log("title: "+title, "file: "+file, "chapter: "+chapter, "bookkey: "+bookkey);
            }
        });
    }

    //SUBMIT LIGHTBOX SECTION
    $(document).on("submit","#vb-modal-section form",function(e){
        e.preventDefault();
        let input = $("#vb-section input[type=hidden]");
        let section = $(this).find("input.content-name").val();
        let file = input.val();
        let bookkey = $("input#vb-ttl-cdidtfyr").data("bookid");
        let chapter = input.data("chapter") - 1;
        let book = $("h1#vb-full-title").data("title");
        let bookIndex = input.data("bookindex");

        addContent(bookkey,section,chapter,book,file,bookIndex);
        $(this).unbind();
    });

    /*** END BOOK SECTION ***/

    //SHOW LIGHTBOX EDITOR
    window.showEditor = function(chapter,key,name,file){
        $.ajax({
            method: "POST",
            url: "../pages/parts/lightbox-editor.php",
            data: {chapter:chapter,key:key,name:name,file:file},
            dataType: "text",
            success: function(data){
                $("#vb-modal-container").html(data);                 
            }
        });
    }

    //ADD CONTENT IN DATABASE
    window.addContent = function(id,name,chapter,book,file = "",bookIndex){
        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {id:id,index:bookIndex,name:name,chapter:chapter,title:book,action:"add"},
            dataType: "text",
            success: function(data){
                let key = data - 1;
                loadChapterPart(chapter);
                if(file.length > 0){
                    showEditor(chapter,key,name,file);                    
                }
                //$("body").prepend(data);
            }
        })
    }

    //GET LIGHTBOX EDITOR
    $(document).on("click",".list-item-vbcontent span.showing-lightbox",function(){
        let chapter = $(this).data("chapter");
        let key = $(this).data("key");
        let name = $(this).parents(".list-item-vbcontent").find("span:first-child").text();
        let file = $(this).data("name");

        showEditor(chapter,key,name,file);

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

        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {text:text,key:key,file:file,action:"update"},
            dataType: "text",
            success: function(data){
                setTimeout(function(){
                    loadEditStyle(chapter,key,title,file);
                    //$("#vb-modal-editor, .modal-backdrop").remove();
                    loadChapterPart(data);
                    //$("#btn-content"+key).html('<i class="fa fa-pencil text-muted" data-status="1" aria-hidden="true"></i>');
                },1500);
            }
        })
    });
    
    //LOAD EDIT STYLE
    window.loadEditStyle = function(chapter,content,title,lctn){
        //console.log("Load Edit");
        $.ajax({
            method:"POST",
            url:"../pages/parts/edit-style.php",
            data: {chapter:chapter,content:content,title:title,file:lctn},
            dataType: "text",
            success: function(data){
                $("#vb-modal-container").html(data);
            }
        });
    }

    //GET MODAL DELETE
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
    function previewPart(chapter,content,title,lctn){
        $.ajax({
            method:"POST",
            url:"../pages/parts/content-part-preview.php",
            data: {chapter:chapter,content:content,title:title,file:lctn},
            dataType: "text",
            success: function(data){
                $("#vb-modal-container").html(data);
            }
        });
    }

    //PREVIEW CHAPTER PART
    $(document).on("click",".vb-view-content, .back-to-preview",function(){
        let content = $(this).data("key");
        let title = $(this).data("title");
        let chapter = $(this).data("chapter");
        let lctn = $("#vb-ttl-cdidtfyr").data("universal");
        previewPart(chapter,content,title,lctn);
    });

    //EDIT BOOK STYLE
    $(document).on("click","li.list-item-vbcontent button.edit-vb-style",function(){
        let file = $("#vb-ttl-cdidtfyr").data("universal");
        let chapter = $(this).data("chapter");
        let key = $(this).data("key");
        let title = $(this).parents("li.list-item-vbcontent").find("span.vb-cnt-title").text();
        window.loadEditStyle(chapter,key,title,file);
    });

});