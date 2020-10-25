<?php

if(isset($_POST['data'])){
    if(isset($_COOKIE['userdata'])){
        $UID = $_COOKIE['userdata']['id'];
        $UName = $_COOKIE['userdata']['name'];
    }
    $UFolder = "{$UName}{$UID}";
    $book = $_POST['data'];
    $key = $book - 1;
    $allBooks = file_get_contents("../json/users/bookdata/{$UFolder}/books-list-title.json");
    $books = json_decode($allBooks);
    $thisChapters = $books[$key]->chapter;
    $filename = $books[$key]->storage;
    $bookContents = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/$filename.json");
    $bookContents = json_decode($bookContents);
?>
<div class="col-md-12" style="margin-top:60px;">
    <div class="bg-light mt-4 mb-3 px-5 py-2 d-flex justify-content-between">
        <p class="m-0 p-2">Download as HTML5! </p>
        <button id="vb-download" class="btn btn-primary">Download</button>
    </div>    
</div>
<div class="col-md-12 my-4 d-none">
    <button id="vb-showMenu" class="btn btn-secondary float-right mr-3">Menu</button>
</div>
<div class="col-md-12">
<div id="vb-control-wrap" class="pb-4 pt-2 px-5">
    <span id="vb-zoomvalue">160%</span> <input type="range" id="vb-sliderzoomer" value="6" min="0" max="10" step="2" id="vb-zoomer">
</div>
<div id="book-container" data-actBG="0"></div>
</div>
<div class="col-md-12">
<nav class="p-5" aria-label="Book page navigation">
  <ul class="pagination" id="vbPageNav" data-prev="-1" data-next="0">
    <li class="page-item">
        <a data-nav="prev" class="bookprev" href="#" style="display:none;">
            <span class="arrow">
                <span class="tip"></span>
                <span class="shaft"></span>
            </span>
        </a>
    </li>
    <li class="page-item">
        <a data-nav="next" class="booknext" href="#">
            <span class="arrow">
                <span class="tip"></span>
                <span class="shaft"></span>
            </span>
        </a>
    </li>
  </ul>
</nav>
</div>
<audio id="vb-audioplayer" src="" class="d-none"></audio>

<script type="text/javascript">
const book = <?php echo $key; ?>;
const loadBook = function(book = <?php echo $key; ?>, chapter = 0, section = 0, parts = 0){
    let url = "book.php";
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
    loadBook(book,0,0,1);    
});

const PlaySound = function(File,Dir,vol,Status){    
    
    if(Status === 0){
        let path = (Dir == 1) ? "user/" : "";
        let Sound = $("#vb-audioplayer")[0];
        Sound.src='../../media/sounds/'+path+File;
        Sound.volume = vol;
        Sound.loop = true;
        $("div.vbPages").attr("data-status",0);
        return Sound; 
    }else{
        return null;
    }
    
}

const ProcessSound = function(){
    let active = $("div.activePage");
    let File = active.data("sound");
    let volume = active.data("volume");
    let delay = active.data("delay");
    let dir = active.data("sdir");
    let status = active.data("status");    
    if(status !== undefined){        
        let Sound = PlaySound(File,dir,volume,status);
        setTimeout(function(){        
            if(status < 1){
                Sound.play();
                let = status = null;
            }else{
                Sound.pause();
                let = status = null;
            }
        },delay);
    }
}

$(document).on("click","ul.vb-section-list-nav li",function(){
    if(!$(this).hasClass("act-section")){
        let File = $(this).data("sound");
        let dir = $(this).data("sdir");
        //let delay = $(this).data("delay");
        let status = $("ul.vb-section-list-nav > li").data("status");
        let index = $(this).data("nav");
        let chapter = $(this).data("chapter");
        let section = $(this).data("section");
        let Sound = PlaySound(File,dir,status);
        //setTimeout(function(){        
            if(status < 1){
                Sound.play();
                let = status = null;
            }else{
                Sound.pause();
                let = status = null;
            }
            console.log("Played");
        //},delay);
        pageNav(index,"",false);
        console.log(index);
        $("ul.vb-section-list-nav > li").removeClass("act-section");
        $(this).addClass("act-section");
        $("#book-navigation-container").addClass("d-none");
        $("div#book-container").html(vbloader);
        $("div#heading"+chapter+" button.collapsed").click();
               
        setTimeout(function(){
            loadBook(book,chapter,section,1);
        },700);
    }else{
        let Sound = null;
        let status = null;
    }
});

$(document).on("click","span.x-close, #vbBookCover",function(){
    $("#book-navigation-container").addClass("d-none");
});

$(document).on("click","#vb-showMenu",function(){
    $("#book-navigation-container").removeClass("d-none");
});

$(document).on("click","button#vb-download",function(){
    bookDownloadData();
});

$("main.main-editor").removeClass("main-editor");

const viewContent = function(container){
    let editor = QuillEditor(container,true,false);
    return editor;
}

const loadBooksContent = function(){
    let contentPage = $('div.vbPageContent');
    let content = contentPage.length;
    let view;
    for(i=0;content>i;i++){
        $.ajax({
            url: "../model/books.php",
            method: "POST",
            data: {file:"<?php echo $filename; ?>",path:"<?php echo $UFolder; ?>",section:i,action:"loadBC"},
            dataType: "text",
            success: function(data){            
                    let obj = JSON.parse(data);                
                    let container = ".vbPage"+obj.id;
                    let text = obj.content;
                    text = JSON.parse(text);
                    view = viewContent(container);
                    view.setContents(text);      
                    //console.log(obj);     
            }
        });
    }
}

//PAGINATION
setTimeout(function(){ 
    loadBooksContent();
    changeBG(0);
},1000);

$(document).on('click','div.vbChapter-wrap .tbcLink',function(){
    let n = $(this).data("page");
    let pages = $("#bookWrapQuill > div.vbPages");
    let allPages = pages.length - 1;
    $("#vbPageNav").data("next",n);
    $("div.vbPages").addClass("d-none");
    $("div.vbPages").removeClass("activePage");
    $(pages[n]).removeClass("d-none");
    $(pages[n]).addClass("activePage");
    if(allPages === n){
        $("a.booknext").addClass("d-none");
        $("#vbPageNav > li:last-child").prepend("<span>End</span>");
    }
    $('html, body, div#book-container').animate({scrollTop:0}, 250);
    ProcessSound();
});

//CHANGE BG
const changeBG = function(x){
    let bgData = $("#bookWrapQuill > div.vbPages");
    let bgType = $(bgData[x]).data("bgtype");
    let bgValue = $(bgData[x]).data("background");
    let bgStyle = (bgType == "color") ? bgValue : "url(../../media/background/"+bgValue+")";
    let bgResult = (bgValue != "") ? bgStyle : "#fff";
    $("div#book-container").css("background",bgResult);
    //$("div#book-container").data("actbg",x);
    //console.log(bgType,bgStyle,x);
}

$(document).on("click","#vbPageNav > li > a",function(e){
    e.preventDefault();    
    let nav = $(this).data("nav");
    let i = $("#vbPageNav").data("next"); 
    let pages = $("#bookWrapQuill > div.vbPages");
    let actBG = $("div#book-container").data("actbg");
    let crrntPg;
    let allPages = pages.length - 1;
    if(nav == "next"){
        $(pages[i]).addClass("d-none");
        $(pages[i]).removeClass("activePage");
        i++;
        $("#vbPageNav").data("next",i);
        $(pages[i]).removeClass("d-none");
        $(pages[i]).addClass("activePage");
        changeBG(i);

    }else{
        $(pages[i]).addClass("d-none");
        $(pages[i]).removeClass("activePage");
        i--;
        $("#vbPageNav").data("next",i);
        $(pages[i]).removeClass("d-none");        
        $(pages[i]).addClass("activePage");
        changeBG(i);
    }
    
    if(i > 0){
        $("a.bookprev").css("display","block");
    }else{
        $("a.bookprev").css("display","none");
    }
    if(allPages === i){
        $("a.booknext").addClass("d-none");
        $("#vbPageNav > li:last-child").prepend("<span>End</span>");
    }else if(allPages === i+1){
        $("a.booknext").removeClass("d-none");
        $("#vbPageNav > li:last-child > span").remove();
    }
    $('html, body, div#book-container').animate({scrollTop:0}, 250);

    ProcessSound();
});

//ZOOMER
$(document).on('input', '#vb-sliderzoomer', function(){
    let value = $(this).val();
    let zoom;
    let pb;
    switch(parseInt(value)){
        case 0: zoom = 80; pb = 0; break;
        case 2: zoom = 100; pb = 0; break;
        case 4: zoom = 120; pb = 0; break;
        case 6: zoom = 160; pb = 6; break;
        case 8: zoom = 200; pb = 12; break;
        case 10: zoom = 240; pb = 15; break;
    }
    $("#book-container").css("zoom",zoom+"%");
    $("#book-container").css({"-moz-transform":"scale("+zoom+"%,"+zoom+"%)","-moz-transform-origin":"top"});
    $("#book-container").css({"-ms-transform":"scale("+zoom+"%,"+zoom+"%)","-ms-transform-origin":"top"});
    $("#book-container").css("-webkit-zoom",zoom+"%");
    $("#book-container").css("padding-bottom",pb+"rem");
    $("span#vb-zoomvalue").text(zoom+"%");

});

$(document).ready(function(){
    let main = $("main");
    let bookwrap = $("div#book-container");
    main.removeClass("container");
    main.addClass("container-fluid");
    main.addClass("p-fixed");
    bookwrap.css({"-moz-transform":"scale(1)","zoom":"160%","-webkit-zoom":"160%","-ms-transform":"scale(1)","padding-bottom":"6rem"});
});
</script>

<?php

}