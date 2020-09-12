<?php

if(isset($_POST['data'])){
    $book = $_POST['data'];
    $key = $book - 1;
    $allBooks = file_get_contents("../json/books-list-title.json");
    $books = json_decode($allBooks);
    $thisChapters = $books[$key]->chapter;
    $filename = $books[$key]->storage;
    $bookContents = file_get_contents("../json/book-content/$filename.json");
    $bookContents = json_decode($bookContents);
?>
<div class="p-fixed d-none" id="book-navigation-container">
    <div id="vbBookCover"></div>
</div>
<div class="col-md-12">
    <div class="bg-light mt-4 mb-3 px-5 py-2 d-flex justify-content-between">
        <p class="m-0 p-2">Download as HTML5! </p>
        <button id="vb-download" class="btn btn-primary">Download</button>
    </div>    
</div>
<div class="col-md-12 my-4 d-none">
    <button id="vb-showMenu" class="btn btn-secondary float-right mr-3">Menu</button>
</div>
<div class="col-md-12">
    <div id="book-container"></div>
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
        let Sound = $("#vb-audioplayer")[0];
        Sound.src='../../media/sounds/'+path+File;
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
    let dir = active.data("sdir");
    let status = active.data("status");    
    if(status !== undefined){        
        let Sound = PlaySound(File,dir,status);
        if(status < 1){
            Sound.play();
            let = status = null;
        }else{
            Sound.pause();
            let = status = null;
        }
    }
}

$(document).on("click","ul.vb-section-list-nav li",function(){
    if(!$(this).hasClass("act-section")){
        let File = $(this).data("sound");
        let dir = $(this).data("sdir");
        let status = $("ul.vb-section-list-nav > li").data("status");
        let index = $(this).data("nav");
        let chapter = $(this).data("chapter");
        let section = $(this).data("section");
        let Sound = PlaySound(File,dir,status);
        if(status < 1){
            Sound.play();
            let = status = null;
        }else{
            Sound.pause();
            let = status = null;
        }
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

// const pageNav = function(i,nav,arg){       

//     if(arg){
//         let x = (nav == "next") ? i+1 : i;
//         let y = (nav == "prev") ? i-1 : i;
//         let page = (nav == "next") ? $("li[data-nav="+x+"]") : $("li[data-nav="+y+"]");
//         if(nav == "next"){
//             $("#vbPageNav").data("next",x); 
//         }else{
//             $("#vbPageNav").data("next",y); 
//         }
//         return page;
//     }else{
//         $("#vbPageNav").data("next",i);
//     }
    
// }


// $(document).on("click","#vbPageNav > li > a",function(e){
//     e.preventDefault();    
//     let nav = $(this).data("nav");
//     let i = $("#vbPageNav").data("next"); 
    
//     if(i !== -1 && !(i == 0 && nav == "prev")){
//         pageNav(i,nav,true).click();
//         $('html, body, div#book-container').animate({scrollTop:0}, 250);
//     }    
// });

//PAGINATION
setTimeout(function(){
    // const options = {
    //     debug: false,
    //     placeholder: false,
    //     modules: {
    //         toolbar: false
    //     },
    //     readOnly: true,
    //     theme: 'snow',
    //     scrollingContainer: false
    // };

    const viewContent = function(container){
        let editor = QuillEditor(container,true,false);
        return editor;
    } 

    $.ajax({
        url: "../json/book-content/<?php echo $filename; ?>.json",
        method: "GET",
        dataType: "JSON",
        success: function(json){
            
            for(let i = 0; i < json.length; i++) {                  
                let obj = json[i];
                let view;
                let container = ".vbPage"+obj.id;
                let text = obj.content;
                text = JSON.parse(text);
                view = viewContent(container);
                view.setContents(text);
                //console.log(text);
           
            }
        }
    });
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

$(document).on("click","#vbPageNav > li > a",function(e){
    e.preventDefault();    
    let nav = $(this).data("nav");
    let i = $("#vbPageNav").data("next"); 
    let pages = $("#bookWrapQuill > div.vbPages");
    let allPages = pages.length - 1;
    console.log(allPages);
    if(nav == "next"){
        $(pages[i]).addClass("d-none");
        $(pages[i]).removeClass("activePage");
        i++;
        $("#vbPageNav").data("next",i);
        $(pages[i]).removeClass("d-none");
        $(pages[i]).addClass("activePage");
    }else{
        $(pages[i]).addClass("d-none");
        $(pages[i]).removeClass("activePage");
        i--;
        $("#vbPageNav").data("next",i);
        $(pages[i]).removeClass("d-none");        
        $(pages[i]).addClass("activePage");
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

    //console.log("Total: "+ allPages, "Current: "+ i);
    ProcessSound();
});
</script>

<?php

}