<?php

if(isset($_POST['data'])){
    $book = $_POST['data'];
    $key = $book - 1;
    $allBooks = file_get_contents("../json/books-list-title.json");
    $books = json_decode($allBooks);
    $thisChapters = $books[$key]->chapter;
    //$thisChapters = json_decode($thisChapters);
    //print_r($thisChapters);
?>
<div class="p-fixed d-none" id="book-navigation-container">
    <div id="vbBookCover" style="background: url(/media/bookcover/user/Thyroid-book-.jpg-142dfbf.jpg);"></div>
</div>
<div class="col-md-12">
    <div class="bg-light mt-4 mb-3 px-5 py-2 d-flex justify-content-between">
        <p class="m-0 p-2">Download as HTML5! </p>
        <button id="vb-download" class="btn btn-primary">Download</button>
    </div>    
</div>
<div class="col-md-12 mt-4">
    <button id="vb-showMenu" class="btn btn-secondary float-right mr-3">Menu</button>
</div>
<div class="col-md-12">
    <div id="book-container"></div>
</div>

<script type="text/javascript">
const book = <?php echo $key; ?>;
const loadBook = function(book = <?php echo $key; ?>, chapter = 0, section = 0, parts = 0){
    let url = (parts === 0) ? "navigation.php" : "book.php";
    $.ajax({
        url: "/pages/book/"+url,
        method: "POST",
        data: {book:book,chapter:chapter,section:section,file: "<?php echo $books[$key]->storage; ?>"},
        dataType: "text",
        beforeSend: function(){

        },success: function(data){
            if(parts === 0){
                $("div#book-navigation-container").append(data);
            }else{
                $("div#book-container").html(data);
            }
        }
    });
}

const bookDownloadData = function(){
    let storage = '<?php echo $books[$key]->storage; ?>';
    let title = '<?php echo $books[$key]->title; ?>';
    let subtitle = '<?php echo $books[$key]->subtitle; ?>';
    let chapter = `<?php $html = ""; foreach($thisChapters as $k => $value){ 
        $json = json_decode($value); 
        //$chapter = str_replace('"','\"',$json->name);
        //$chapter = str_replace("'","\'",$chapter);
        //$html .= '{"chapter'.$k.'":"'.$chapter.'"},'; 
        $html .= "$json->name |,";
        } echo rtrim($html,"|,"); ?>`;

    $.ajax({
        url: "/pages/downloads/download.php",
        method: "POST",
        data: {book:<?php echo $key; ?>,chapters:chapter,title:title,subtitle:subtitle,file:storage,action:'create'},
        dataType:"text",
        success: function(response){
            window.open(response, '_blank');
            console.log(response);
        }
    });
}

$(document).ready(function(){    
    loadBook(book);
    loadBook(book,0,0,1);
});

const PlaySound = function(File,Dir,Status){    
    
    if(Status === 0){
        let path = (Dir == 1) ? "user/" : "";
        let Sound = document.createElement('audio');
        Sound.src='../../media/sounds/'+path+File;
        $("ul.vb-section-list-nav > li").attr("data-status",1);
        return Sound; 
    }else{
        return null;
    }
    
}

$(document).on("click","ul.vb-section-list-nav > li",function(){
    if(!$(this).hasClass("act-section")){
        let File = $(this).data("sound");
        let dir = $(this).data("sdir");
        let status = $("ul.vb-section-list-nav > li").data("status");
        let Sound = PlaySound(File,dir,status);
        if(status < 1){
            Sound.play();
            let = status = null;
        }else{
            Sound.pause();
            let = status = null;
        }
        $("ul.vb-section-list-nav > li").removeClass("act-section");
        $(this).addClass("act-section");
        let chapter = $(this).data("chapter");
        let section = $(this).data("section");
        $("#book-navigation-container").addClass("d-none");
        $("div#book-container").html(vbloader);
        setTimeout(function(){
            loadBook(book,chapter,section,1);
        },700);
    }else{
        let Sound = null;
        let status = null;
    }
});

$(document).on("click","span.x-close",function(){
    $("#book-navigation-container").addClass("d-none");
});

$(document).on("click","#vb-showMenu",function(){
    $("#book-navigation-container").removeClass("d-none");
});

$(document).on("click","button#vb-download",function(){
    bookDownloadData();
});
</script>

<?php

}