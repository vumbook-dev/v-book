jQuery(document).ready(function(){
    const bookKey = $("h1#vb-full-title").data("book");
    //LIST BOOK CHAPTER
    function listBookChapters(key){
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
            url: "../controller/chapters.php",
            data: {key:bookKey,chapter:chapter,action:"add"},
            dataType: "text",
            success: function(){
                //alert("New Book Added: "+data);
                listBookChapters(bookKey);
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
            $("form").reset();
        },500);
    });

    //LOAD CHAPTER PART
    function loadChapterPart(chapter){
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
                //console.log(title);
            }
        });
    }

    //SHOW CHAPTER PARTS
    $(document).on("click","button.btn-chapter",function(){
        let chapter = $(this).data("chapter");
        let content = $("div#vbcontent-list"+chapter).html();
        if(content.length === 0){
            loadChapterPart(chapter);
        }
        //console.log(content.length);
    });

    //ADD CONTENT IN DATABASE
    function addContent(id,name,key,title){
        $.ajax({
            method: "POST",
            url: "../controller/content.php",
            data: {id:id,name:name,chapter:key,title:title,action:"add"},
            dataType: "text",
            success: function(data){
                loadChapterPart(key);
            }
        })
    }

    //SUBMIT CONTENT TITLE
    $(document).on('click','.vb-new-content',function(){
        let parent = $(this).parents(".tc-wrap");
        let input = parent.find("input.content-name");
        let content = input.val();
        let id = parent.find("input[type=hidden]").val();
        let key = $(this).data("key");
        let title = parent.find("input[type=hidden]").data("title");

        if(content.length != 0){
            addContent(id,content,key,title);
        }        

        input.val("");
    });

    //SHOW LIGHTBOX EDITOR
    function showEditor(chapter,key,name,file){
        $.ajax({
            method: "POST",
            url: "../pages/parts/lightbox-editor.php",
            data: {chapter:chapter,key:key,name:name,file:file},
            dataType: "text",
            success: function(data){
                $("#editors-modal-container").html(data);
            }
        });
    }

    //GET LIGHTBOX EDITOR
    $(document).on("click",".list-item-vbcontent>span",function(){
        let chapter = $(this).data("chapter");
        let key = $(this).data("key");
        let name = $(this).parent(".list-item-vbcontent").find("span:first-child").text();
        let file = $(this).data("name");
        showEditor(chapter,key,name,file);
    });

    //REMOVE LIGHTBOX
    $(document).on("click","#vb-modal-editor button.close",function(){
        $("#vb-modal-editor, .modal-backdrop").remove();
    });

    //ADD CONTENT TO CHAPTER PART
    $(document).on("click","#vb-submit-content",function(){
        let text = $(".ck-editor__main div.ck-editor__editable").html();
        let key = $(this).data("key");
        let file = $(this).data("file");

        $.ajax({
            method: "POST",
            url: "../controller/content.php",
            data: {text:text,key:key,file:file,action:"update"},
            dataType: "text",
            success: function(data){
                alert(data);
                setTimeout(function(){
                    $("#vb-modal-editor, .modal-backdrop").remove();
                },1500);
            }
        })
    });


});