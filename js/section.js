jQuery(document).ready(function($){
    /*** START BOOK SECTION ***/
    
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
            addContent(id,content,key,title,"",bookIndex);
            $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert"><i class="fa fa-check-circle-o" aria-hidden="true"></i> '+input.val()+' Successfully Added</div>');
            setTimeout(function(){
                $("div.message-status").remove();
            },4000);
        }        

        input.val("");
    });

    //LOAD LIGHTBOX FOR ADDING NEW SECTION
    // window.addSectionLightbox = function(title,file,chapter,index){        
    //     $.ajax({
    //         method: "POST",
    //         url: "../pages/parts/section-lightbox.php",
    //         data: {title:title,file:file,chapter:chapter,bookIndex:index},
    //         dataType: "text",
    //         success: function(data){
    //             $("#vb-modal-container").html(data);              
    //             //console.log("title: "+title, "file: "+file, "chapter: "+chapter, "bookkey: "+bookkey);
    //         }
    //     });
    // }

    //SUBMIT LIGHTBOX SECTION
    // $(document).on("submit","#vb-modal-section form",function(e){
    //     e.preventDefault();
    //     let input = $("#vb-section input[type=hidden]");
    //     let section = $(this).find("input.content-name").val();
    //     let file = input.val();
    //     let bookkey = $("input#vb-ttl-cdidtfyr").data("bookid");
    //     let chapter = input.data("chapter") - 1;
    //     let book = $("h1#vb-full-title").data("title");
    //     let bookIndex = input.data("bookindex");

    //     addContent(bookkey,section,chapter,book,file,bookIndex);
    //     $(this).unbind();
    // });

    /*** END BOOK SECTION ***/
});