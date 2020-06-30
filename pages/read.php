<?php

if(isset($_POST['data'])){
    $book = $_POST['data'];
    $key = $book - 1;
    $allBooks = file_get_contents("../json/books-list-title.json");
    $books = json_decode($allBooks);

?>

<div class="col-md-8">
    <div id="book-container"></div>
</div>

<div class="col-md-4">
    <div id="book-navigation-container">

    </div>
</div>

<script type="text/javascript">
const book = <?php echo $key; ?>;
const loadBook = function(book = <?php echo $key; ?>, chapter = 0, section = 0, parts = 0){
    let url = (parts === 0) ? "navigation.php" : "book.php";
    $.ajax({
        url: "/pages/book/"+url,
        method: "POST",
        data: {book:book,chapter:chapter,section:section,file: "<?php echo $books[0]->storage; ?>"},
        dataType: "text",
        beforeSend: function(){

        },success: function(data){
            if(parts === 0){
                $("div#book-navigation-container").html(data);
            }else{
                $("div#book-container").html(data);
            }
        }
    });
}

$(document).ready(function(){
    loadBook(book);
    loadBook(book,0,0,1);
});

$(document).on("click","ul.vb-section-list-nav > li",function(){
    $("ul.vb-section-list-nav > li").removeClass("act-section");
    $(this).addClass("act-section");

    let chapter = $(this).data("chapter");
    let section = $(this).data("section");
    loadBook(book,chapter,section,1);

});
</script>

<?php

}