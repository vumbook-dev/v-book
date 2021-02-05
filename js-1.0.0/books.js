jQuery(document).ready(function($){
    //LIST BOOK TITLE
    function listBooks(){
        $.ajax({
            method: "GET",
            url: "../pages/parts/title-list.php",
            success: function(data){
                $("div.book-list").html(data);
            }
        });
    }
    listBooks();

    //CREATE A JSON BOOK DATA
    function createBook(title,sub,template){
        $.ajax({
            method: "POST",
            url: "../model/books.php",
            data: {title:title,subTitle:sub,template:template,action:"add"},
            dataType: "text",
            success: function(data){
                //alert("book-chapter/book="+data);
                //listBooks();
                //input.val("");
                window.location.replace("/table-of-contents/book="+data);
            }
        });
    }

    //SUBMIT NEW BOOK
    $(document).on("submit",".cb-wrap > form",function(e){
        e.preventDefault();
        let titleInput = $(this).find("input[name=book-title]");
        let title = titleInput.val();
        let subInput = $(this).find("input[name=sub-title]");
        let subtitle = subInput.val();
        let template = $(this).find("select[name=template]").val();
        //$(this).off(e);
        createBook(title,subtitle,template);
        //$(this).reset();
    });    

});

