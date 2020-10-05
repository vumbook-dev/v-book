jQuery(document).ready(function($){
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
                //addSectionLightbox(chptr['title'],chptr['file'],chptr['chapter'],chptr['index']);
                input.val("");
                $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Chapter '+chptr['title']+' Successfully Added</div>');
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
    function readURL(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            
            reader.onload = function(e) {
                $('#prev-img-bookcover').attr('src', e.target.result);                
                $('#prev-img-bookcover').removeClass('d-none');
                $("div#book-cover-preview-wrap > i").addClass("d-none");
                $("#upload").text("Update");
                //$('#submit-book-cover input[type=hidden]').val(bookIndex);            
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
        let img = $("#prev-img-bookcover");
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
                $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-success" role="alert"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Book Cover Successfully Uploaded</div>');
                setTimeout(function(){
                    $("div.message-status").remove();
                },4000);
                $("form#submit-book-cover").addClass("input-empty");
                $("#vbUploadBookCover input.rdnly-plchldr").addClass("d-none");
                $("form#submit-book-cover button.btn-primary").addClass("d-none");
                $("#vbUploadBookCover span.fileUpload").removeClass("d-none");                
                //img.attr("src",data);
                //img.removeClass("d-none");
                //$(this).removeEventListener();
                //selfsubmit.clear();       
                //$(".modal-body").prepend(data);               
            }  
        }); 
    });
    
    /*** BOOK COVER SCRIPT END ***/

    //LOAD CHAPTER EDITOR
    const loadChEditor = function(chapter){
        $.ajax({
            method:"POST",
            url:"../pages/parts/chapter-editor.php",
            data: {book:bookKey,chapter:chapter},
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
    const updateChPage = function(chapter,sound,volume,color,title,subtitle) {
        $.ajax({
            method: "POST",
            url: "/model/chapters.php",
            data: {book:bookKey,chapter:chapter,sound:sound,volume:volume,color:color,title:title,subtitle:subtitle,action:"update"},
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
        let title = $("h1.ch-main-title").text();
        let subtitle = $("p.ch-subtitle").text();
        let actSound = $("span.act-sound").data("id");
        let vol = $("input[name=vb-volume-control]").val();
        let color;
        let inputColor = $("div.bgContainer>div.custom-radio input#bgColor:checked");
        if(inputColor.length > 0){
            color = $("div.colorPick-wrap input.pcr-result").val();
        }

        updateChPage(key,actSound,vol,color,title,subtitle);
    });

});