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
    $(document).on('submit','.bc-wrap > form',function(e){
        e.preventDefault();
        let input = $("#chapter-name");
        let chapter = input.val();
        addBookChapter(chapter,input);
    });
});