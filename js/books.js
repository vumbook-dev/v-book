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
    function createBook(title,input,sub,subInput){
        $.ajax({
            method: "POST",
            url: "../model/books.php",
            data: {title:title,subTitle:sub,action:"add"},
            dataType: "text",
            success: function(){
                //alert("New Book Added: "+data);
                listBooks();
                input.val("");
                subInput.val("");
                //loadPage("create-books","");
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
        //$(this).off(e);
        createBook(title,titleInput,subtitle,subInput);
        //$(this).reset();
    });    

});

