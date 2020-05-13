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

    //ADD CONTENT IN DATABASE
    function addContent(id,name,key,chapter){
        $.ajax({
            method: "POST",
            url: "../controller/content.php",
            data: {id:id,content:name,key:key,chapter:chapter,action:"add"},
            dataType: "text",
            success: function(data){
                alert(data);
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
        let chapter = $(this).data("chapter");

        addContent(id,content,key,chapter);
        //alert("It's working "+content);
        input.val("");
    });

});