<?php
require_once "../config.php";
if(isset($_POST['data'])){
    if(isset($_COOKIE['userdata'])){
        $UID = $_COOKIE['userdata']['id'];
        $UName = $_COOKIE['userdata']['name'];
    }
    $UFolder = DATAPATH;
    $book = $_POST['data'];
    $key = $book - 1;
    $allBooks = file_get_contents("../json/users/bookdata/{$UFolder}/books-list-title.json");
    $books = json_decode($allBooks);
    echo count($books);
    $thisChapters = $books[$key]->chapter;
    $filename = $books[$key]->storage;
    // $bookContents = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/$filename.json");
    // $bookContents = json_decode($bookContents);
?>
<div class="col-md-12" id="download-wrap">
    <div class="bg-light mt-4 mb-3 px-5 py-2 d-none justify-content-between">
        <p class="m-0 p-2">Download as HTML5! </p>
        <button id="vb-download" class="btn btn-primary">Download</button>
    </div>    
</div>
<div class="col-md-12 my-4 d-none">
    <button id="vb-showMenu" class="btn btn-secondary float-right mr-3">Menu</button>
</div>
<div class="col-md-12" id="content-wrap">
<div class="lordicon-loader py-5">
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<lottie-player src="https://assets10.lottiefiles.com/packages/lf20_30nris2g.json"  background="transparent"  speed="1"  style="width: 100px; height: 100px;"  loop  autoplay></lottie-player>
<p class="text-center text-muted h5">Collecting Book's Important Files...</p>
</div>
<div id="vb-control-wrap" class="pb-4 pt-2 px-5">
    <span id="vb-zoomvalue">160%</span> <input type="range" id="vb-sliderzoomer" value="6" min="0" max="8" step="2">
</div>
<div <?php echo ($books[$key]->template == 'book') ? 'id="book-container"' : 'id="newspaper-container"'; ?> class="sixteen-hundred" data-actBG="0">
    <nav aria-label="Book page navigation">
    <ul class="pagination" id="vbPageNav" data-prev="-1" data-next="0">
        <li class="page-item">
            <a data-nav="prev" class="bookprev" href="#">
                <span class="arrow">
                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                </span>
            </a>
        </li>
        <li class="page-item">
            <a data-nav="next" class="booknext" href="#">
                <span class="arrow">
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                </span>
            </a>
        </li>
    </ul>
    </nav>
</div>
</div>
<audio id="vb-audioplayer" src="" class="d-none"></audio>

<script type="text/javascript">
const book = <?php echo $key; ?>;
const loadBook = function(book = <?php echo $key; ?>){
    let url = "<?php echo $books[$key]->template; ?>.php";
    $.ajax({
        url: "/pages/template/"+url,
        method: "POST",
        data: {book:book,file: "<?php echo $books[$key]->storage; ?>"},
        dataType: "text",
        beforeSend: function(){
            $('div#vb-control-wrap, div#book-container, div#newspaper-container').addClass('d-none');
        },success: function(data){
            $('div.lordicon-loader').addClass('d-none');
            if(url === 'book.php'){
                $("div#book-container").append(data);
                $('div#vb-control-wrap, div#book-container').removeClass('d-none');
            }else{
                $("div#newspaper-container").html(data);
                $('div#vb-control-wrap, div#newspaper-container').removeClass('d-none');
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
    loadBook(book);    
});

$("main.main-editor").removeClass("main-editor");

<?php if($books[$key]->template === "newspaper"){ ?>
    const viewContent = function(container){
        let editor = QuillEditor(container,null,true,false);
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
        <?php if($books[$key]->template == 'newspaper') echo 'changeBG(0);'; ?>
    },1000);

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
                loadBook(book);
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
        $("div#newspaper-container").css("background",bgResult);
        //$("div#book-container").data("actbg",x);
        //console.log(bgType,bgStyle,x);
    }

<?php } //END LOAD IF NEWSPAPER TEMPLATE 

elseif($books[$key]->template == "book"){ ?>

<?php } ?>

//FUNCTION RESPONSIVE CLASS
const responsiveClass = function(zoom){
    let cont = $('#book-container');
    let zoomClass;
    let currentClass = cont.attr('class');
    switch(zoom){
        case 80: zoomClass = "eight-ten"; break;
        case 100: zoomClass = "one-hundred"; break;
        case 120: zoomClass = "twelve-hundred"; break;
        case 160: zoomClass = "sixteen-hundred"; break;
        case 200: zoomClass = "two-hundred"; break
    }
    cont.toggleClass(currentClass);
    cont.toggleClass(zoomClass);
}

//ZOOMER
$(document).on('input', '#vb-sliderzoomer', function(){
    let value = $(this).val();
    let container = $("#<?php echo $books[$key]->template; ?>-container");
    let zoom;
    let pb;
    let scrnMargin;
    let standardScreen;
    let scrn = screen.width;
    switch(parseInt(value)){
        case 0: zoom = 80; pb = 0; break;
        case 2: zoom = 100; pb = 0; break;
        case 4: zoom = 120; pb = 0; break;
        case 6: zoom = 160; pb = 6; break;
        case 8: zoom = 200; pb = 12; break;
    }
    container.css("zoom",zoom+"%");
    container.css({"-moz-transform":"scale("+zoom+"%,"+zoom+"%)","-moz-transform-origin":"top"});
    container.css({"-ms-transform":"scale("+zoom+"%,"+zoom+"%)","-ms-transform-origin":"top"});
    container.css("-webkit-zoom",zoom+"%");
    responsiveClass(zoom);
    if(zoom === 200 && scrn <= 1440){ 
        if(scrn <= 1440 && scrn >= 1175){
            standardScreen = 1440;
            scrnMargin = ((standardScreen - scrn) < 30) ? "6" : Math.round(((standardScreen - scrn) / 30))+9;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }else if(scrn <= 1174 && scrn >= 1050){
            standardScreen = 1174;
            scrnMargin = ((standardScreen - scrn) < 30) ? "20" : Math.round(((standardScreen - scrn) / 30))+23;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }else if(scrn <= 1049 && scrn >= 970){
            standardScreen = 1049;
            scrnMargin = ((standardScreen - scrn) < 30) ? "29" : Math.round(((standardScreen - scrn) / 30))+30;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }else if(scrn <= 969 && scrn >= 821){
            standardScreen = 969;
            scrnMargin = ((standardScreen - scrn) < 30) ? "36" : Math.round(((standardScreen - scrn) / 30))+39;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }else if(scrn <= 820 && scrn >= 700){
            standardScreen = 820;
            scrnMargin = ((standardScreen - scrn) < 30) ? "50.5" : Math.round(((standardScreen - scrn) / 30))+53.5;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }else{
            container.css("margin-left","auto");
        }
    }else if(zoom === 160 && scrn <= 1024){
        if(scrn <= 1024 && scrn >= 969){
            standardScreen = 1024;
            scrnMargin = ((standardScreen - scrn) < 30) ? "14" : Math.round(((standardScreen - scrn) / 30))+17;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }else if(scrn <= 969 && scrn >= 811){
            standardScreen = 969;
            scrnMargin = ((standardScreen - scrn) < 30) ? "20" : Math.round(((standardScreen - scrn) / 30))+23;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }else if(scrn <= 810 && scrn >= 763){
            standardScreen = 810;
            scrnMargin = ((standardScreen - scrn) < 30) ? "31" : Math.round(((standardScreen - scrn) / 30))+34;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }else{
            container.css("margin-left","auto");
        }
    }else if(zoom === 120 && scrn <= 900){
        if(scrn <= 900){
            standardScreen = 900;
            scrnMargin = ((standardScreen - scrn) < 30) ? "4" : Math.round(((standardScreen - scrn) / 30))+7;  
            console.log(scrnMargin);    
            container.css("margin-left","-"+scrnMargin+"%");
        }
    }else{
        container.css("margin-left","auto");
    }
    
    $("#newspaper-container").css("padding-bottom",pb+"rem");
    $("span#vb-zoomvalue").text(zoom+"%");
});

$(document).ready(function(){
    let main = $("main");
    let bookwrap = $("div#<?php echo $books[$key]->template; ?>-container");
    main.removeClass("container");
    main.addClass("container-fluid");
    main.addClass("p-fixed");
    bookwrap.css({"-moz-transform":"scale(1.6)","-moz-transform-origin":"top","zoom":"160%","-webkit-zoom":"160%","-ms-transform":"scale(1.6)","-ms-transform-origin":"top"});
    $("#newspaper-container").css("padding-bottom","6rem");
});
</script>

<?php

}else{
    echo "EMpty";
}