jQuery(document).ready(function($){
    /*** START CONTENT ***/

    //SHOW LIGHTBOX EDITOR
    // window.showEditor = function(chapter,key,name,file){
    //     $.ajax({
    //         method: "POST",
    //         url: "../pages/parts/lightbox-editor.php",
    //         data: {chapter:chapter,key:key,name:name,file:file},
    //         dataType: "text",
    //         success: function(data){
    //             $("#vb-modal-container").html(data);                 
    //         }
    //     });
    // }

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
    // $(document).on("click",".list-item-vbcontent span.showing-lightbox",function(){
    //     let chapter = $(this).data("chapter");
    //     let key = $(this).data("key");
    //     let name = $(this).parents(".list-item-vbcontent").find("span:first-child").text();
    //     let file = $(this).data("name");

    //     showEditor(chapter,key,name,file);

    // });

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
            url:"../pages/parts/editor.php",
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

});