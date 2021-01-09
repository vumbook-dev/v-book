<?php
require_once "../../config.php";
if(isset($_POST['book']) && isset($_POST['file'])){
    if(isset($_COOKIE['userdata'])){
        $UID = $_COOKIE['userdata']['id'];
        $UName = $_COOKIE['userdata']['name'];
        $UFolder = DATAPATH;
    }
    $path = "../../json/users/bookdata/{$UFolder}/";
    $book = $_POST['book'];
    $ch = 0;
    $file = $_POST['file'];
    $bkBG = file_get_contents("{$path}media/user-background.json");
    $bkBG = json_decode($bkBG);
      
    $listChapter = file_get_contents("{$path}book-chapter/{$file}.json");
    $bookData = file_get_contents("{$path}books-list-title.json");
    $bookCover = file_get_contents("{$path}media/user-bookcover.json");
    $bookCover = json_decode($bookCover);
    $chapterData = json_decode($listChapter);
    $bookData = json_decode($bookData);
    $coverKey = $bookData[$book]->cover;
    $chapter = $bookData[$book]->chapter;        
    $subtitle = (strlen($bookData[$book]->subtitle) > 0) ? "<small class='d-block h6'>{$bookData[$book]->subtitle}</small>" : "";
    $title= $bookData[$book]->title;
    $mainTitle = "{$title} {$subtitle}";
    $BGID = (!empty($bookData[$book]->background)) ? $bookData[$book]->background : "";        
    $bookBackground = (!empty($bkBG) && !empty($bkBG[$BGID]->filename)) ? $bkBG[$BGID]->filename : "";
    $bookCoverFile = ($coverKey !== null) ? $bookCover[$coverKey]->filename : ""; 
    
    $contents = file_get_contents("{$path}book-content/{$file}.json");
    $contents = json_decode($contents); 
    $allContentCounts = count($contents);
    $dsound = file_get_contents("../../json/media/default-sounds.json");
    $dsound = json_decode($dsound);
    $msound = file_get_contents("{$path}media/user-sound.json");
    $msound = json_decode($msound);
    //$bgValue = ($bookData[$book]->bgType === "color") ? $bookData[$book]->bgValue : "url(../../media/background/".$bookData[$book]->bgValue.")" ;

    $titleSound = $contents[0]->sound;
    if(!is_numeric($titleSound)){
        $dir = 1;
        $sound = $titleSound;
        $sound = ltrim($sound,"m");
        $sound = $msound[$sound]->filename;
    }else{
        $dir = 0;
        $sound = $titleSound;
        $sound = $dsound[$sound]->filename;
    }

    $volume = (!empty($contents[0]->volume)) ? $contents[0]->volume : 0.5;
    ?>
    
<div class="paper-effect custom-wrapper rbook-template" id="bookWrapQuill">
<div class="book-content trnsf-reset">

    <?php 
    if($bookData[$book]->cover !== null){ ?>
        <!-- <div id="page0" class="vbBookCover-wrap vbPages" data-chapter="0"><img src="<?php echo 'media/bookcover/'.$UFolder.'/'.$bookCover[$coverKey]->filename; ?>" alt="'.$title.'"></div> -->
        <div class="book" style="z-index: 1;">
        <div class="book-wrap face-front active" id="front-cover">
        <span class="right-control"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
        </div>
        </div>
    <?php    $xpage = 4;
    }else{
        $xpage = 3;
    }
    $show1stpage = ($xpage === 4) ? "d-none" : "";
    $tb = $contents[0];
    $chBgType = (!empty($contents[0]->bgType)) ? $contents[0]->bgType : "color";
    $chBackground = (!empty($contents[0]->background)) ? $contents[0]->background : "#fff";
    $chSnd = (!empty($contents[0]->sound)) ? $contents[0]->sound : null;
    $chVol = (!empty($contents[0]->volume)) ? $contents[0]->volume : 0.5;
    $chDelay = (!empty($contents[0]->delay)) ? $contents[0]->delay*1000 : 1*1000;
    if(!is_numeric($chSnd)){
        $chDIR = 1;
        $chSnd = ltrim($chSnd,"m");
        //$chSnd = $msound[$chSnd]->filename;
        $chSnd = (!empty($dsound[$chSnd]->filename)) ? $dsound[$chSnd]->filename : null;
    }else{
        $chDIR = 0;
        $chSnd = (!empty($dsound[$chSnd]->filename)) ? $dsound[$chSnd]->filename : null;
    }

     ?>
<div class="book d-none" style="z-index: 0;">
    <div class="book-wrap">
        <span class="left-control"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
        <h1 class="vb-book-main-title text-center px-5"><?php echo $mainTitle; ?></h1>        
        <span class="right-control sound-data-holder" data-volume="<?php echo $chVol; ?>" data-sound="<?php echo $chSnd; ?>" data-delay="<?php echo $chDelay; ?>" data-status="0" data-sdir="<?php echo $chDIR; ?>"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
    </div>
    <div class="page-arrow-wrap"></div>
</div>
<?php
    $html = '';
    $n = 1;
    $pageNumber = 4;
    $bookInfoNumber = $bookData[$book]->bookInfoSkip;
    $copyrightPageBG = ($chBgType == 'image') ? "background-image:url(/media/page-background/{$UFolder}/{$chBackground});" : "background-color:{$chBackground};";
    //LOAD BOOK INFO PAGES
    for($cp=0; $cp<$bookInfoNumber; $cp++){
        //TEMPLATE FRONT PAGE AND BACK PAGE
        $contentText = $contents[$cp]->content;
        foreach($contentText as $key => $value){
            //$soundCPData = ($key === 0) ? ' sound-data-holder" data-sound="'.$sound.'" data-volume="'.$volume.'" data-delay="'.$delay.'" data-status="0"  data-sdir="'.$dir.'"' : '"';
            $getText = json_decode($value);
            $html .= '<div class="book page-1 d-none" style="z-index: 0;">';
            $html .= '<div class="book-wrap face-'.$chBgType.'" style="'.$copyrightPageBG.'"><span class="left-control"><i class="fa fa-angle-left" aria-hidden="true"></i></span>'.$getText->text;
            $html .= '<span class="right-control"><i class="fa fa-angle-right" aria-hidden="true"></i></span></div>';
            $html .= '<div class="page-arrow-wrap"><p class="text-center page-number">'.$pageNumber.'</p></div></div>';
            $pageNumber += 1;
        }
    }
echo $html;//SHOW BOOK INFORMATION

$html = '';
$n = 0;
$line = 16;
        foreach($chapter as $key => $xi){             
            $x = json_decode($chapter[$key]);
            $chapterName = $x->name; 
            if($line > 15){
                $html .= '<div class="book d-none" style="z-index: 0;"><div class="book-wrap"><span class="left-control"><i class="fa fa-angle-left" aria-hidden="true"></i></span>';
            }
            if($key == 0){
                $html .=  '<h2 class="text-center">Contents</h2>'; 
                $line -= 4;
            }
            
            $html .= '<div class="vbChapter-wrap pb-2">';
            if($key != 0){
                $html .= "<h5 class='tbcLink part-page$key' data-page='$xpage'><div>$chapterName</div></h5>";
                $xpage = $xpage+1;
                $line -= 2;
            }
            $html .= '<ul>';            
                foreach($contents as $k => $val){
                    if($val->chapter == $key && $val->id != 00 && $val->id != 01){
                        $html .= "<li class='tbcLink chapter-page$k' data-page='$xpage'>$val->cpart</li>";
                        $xpage = $xpage+1;
                        $line -= 1;
                    }                
                }
            $html .= '</ul></div>';
            if($line < 5){
                $html .= '<span class="right-control"><i class="fa fa-angle-right" aria-hidden="true"></i></span></div><div class="page-arrow-wrap"><p class="text-center page-number">'.$pageNumber.'</p></div></div>';             
                $pageNumber += 1;
                $line = 16;
            }
        } 
        if($line > 5){
            $html .= '<span class="right-control"><i class="fa fa-angle-right" aria-hidden="true"></i></span></div><div class="page-arrow-wrap"><p class="text-center page-number">'.$pageNumber.'</p></div></div>';             
            $pageNumber += 1;
            $line = 16;
        }       
        echo $html;  // SHOW TABLE OF CONTENTS

    $page = 3;
    $chx = "x";
    $html = '';
    $n = 0;

    foreach($chapterData as $key => $bChapter){             
        $i = $bChapter;
        if($key != 0 ){         
            $chBgType = (!empty($i->bgType)) ? $i->bgType : "color";
            $chBackground = (!empty($i->background)) ? $i->background : "#fff"; 
            $rbPageBG = ($chBgType == 'image') ? "background-image:url(/media/page-background/{$UFolder}/{$chBackground});" : "background-color:{$chBackground};";
            $chvolume = (!empty($i->volume)) ? $i->volume : 0.5; 
            $chdelay = (!empty($i->delay)) ? $i->delay : 1; 
            if(!empty($i->sound)){
                if(!is_numeric($i->sound)){
                    $dir = 1;
                    $chsound = $i->sound;
                    $chsound = ltrim($chsound,"m");
                    $chsound = $msound[$chsound]->filename;
                }else{
                    $dir = 0;
                    $chsound = $i->sound;
                    $chsound = $dsound[$chsound]->filename;
                }
            }else{
                $chsound = null;
            }
            $html .= '<div class="book d-none ql-snow" style="z-index: 0;">';
            $html .= '<div class="book-wrap ql-editor btmp-content face-'.$chBgType.'" style="'.$rbPageBG.'"><span class="left-control"><i class="fa fa-angle-left" aria-hidden="true"></i></span>';
            $html .= "<div class='vbPages vbPagesTitle' id='page{$page}' data-bgtype='{$chBgType}' data-background='{$chBackground}'>$i->name</div>";
            if($key > 1) $pageNumber += 1;
            $html .= '<span class="right-control sound-data-holder" data-volume="'.$chvolume.'" data-sound="'.$chsound.'" data-delay="'.$chdelay.'" data-status="0" data-sdir="'.$dir.'"><i class="fa fa-angle-right aria-hidden="true"></i></span></div><div class="page-arrow-wrap"><p class="text-center page-number page-part-number" data-partpage="'.$key.'">'.$pageNumber.'</p></div></div>';    
            $page = $page+1;            
        }

        foreach($contents as $k => $value){        
            $n = 0;
            $chPtitle = $value->chapter;
            $volume = (!empty($contents[$k]->volume)) ? $contents[$k]->volume : 0.5;
            $delay = (!empty($contents[$k]->delay)) ? $contents[$k]->delay : 1;

            if($value->id != "00" && $value->id != "01" && $k >= $bookInfoNumber){
                if(!is_numeric($value->sound)){
                    $dir = 1;
                    $sound = $value->sound;
                    $sound = ltrim($sound,"m");
                    $sound = $msound[$sound]->filename;
                }else{
                    $dir = 0;
                    $sound = $value->sound;
                    $sound = $dsound[$sound]->filename;
                }
                if($value->chapter == $key && $value->id != 00){
                    $bgType = (!empty($value->bgType)) ? $value->bgType : "color";
                    $bgVal = (!empty($value->background))  ? $value->background : "#fff";                    
                    $rbPageBG = ($bgType == 'image') ? "background-image:url(/media/page-background/{$UFolder}/{$bgVal});" : "background-color:{$bgVal};";
                    $contentText = $value->content;
                    $contentPage = count($contentText);
                    foreach($contentText as $ckey => $v){    
                        $pageNumber += 1;                  
                        $soundData = ($ckey === 0) ? ' sound-data-holder" data-sound="'.$sound.'" data-volume="'.$volume.'" data-delay="'.$delay.'" data-status="0"  data-sdir="'.$dir.'"' : '"';
                        $pgData = ($ckey == 0) ? ' page-chapter-number" data-chapterpage="'.$k.'"' : '"';
                        $getText = json_decode($v);                    
                        $html .= '<div class="book page-'.$ckey.' d-none ql-snow" style="z-index: 0;">';
                        $html .= '<div class="book-wrap ql-editor btmp-content face-'.$bgType.'" style="'.$rbPageBG.'"><span class="left-control"><i class="fa fa-angle-left" aria-hidden="true"></i></span>';
                        $html .= "<div class='vbPage{$value->id} vbPages vbPageContent{$contentPage}' id='page{$page}'  data-bgtype='{$bgType}' data-background='{$bgVal}'>{$getText->text}</div>";
                        $html .= '<span class="right-control'.$soundData.'><i class="fa fa-angle-right" aria-hidden="true"></i></span></div><div class="page-arrow-wrap" '.$soundData.'><p class="text-center page-number'.$pgData.'>'.$pageNumber.'</p></div></div>';                      
                    }
                    //$html .= ($face == 'back') ? : '</div><div class="page-arrow-wrap"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></div></div><div class="face-back"><div class="book-wrap"></div><div class="page-arrow-wrap"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i></div></div></div>';
                }
                
            }                      
            $sound = "";
        }
    }
    //$html .= ($face == 'back') ? : '</div><div class="page-arrow-wrap"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></div></div><div class="face-back"><div class="book-wrap"></div><div class="page-arrow-wrap"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i></div></div></div>';
   echo $html;
    ?>
</div></div>
<audio src="" id="vb-prevAudio" class="d-none"></audio>
<style>
    /* div#book-container{
        background:<?php //echo $bgValue; ?>
    } */
    div.ql-container.ql-snow{
        border:0;
    }
    div.vbPagesTitle{
        padding: 0 1rem;
    }
    div.vbPageContent{
        padding: 2rem;
    }
    div.vbBookCover-wrap.vbPages{
        padding:0;
    }
    div#book-container{
        width: 800px;
        margin: auto;
        height:580px;
        background-size: cover;
        background-repeat: no-repeat!important;
        background-origin: border-box!important;
        background-size: cover!important;
        background-position: bottom!important;
    }
    div#book-container{
        scrollbar-width: none;        
    }
    div.vbChapter-wrap h1{
        padding: 0!important;
        font-size: 1.25rem;
    }
    body{
        background-color: #e7e7e7;
    }
    .bookprev .arrow .shaft, .booknext .arrow .shaft{
        border-color: #e7e7e7;
    }
    #vbPageNav > li.page-item{
        top:60%;
        position:fixed;
    }
    #vbPageNav > li.page-item:first-child{
        left: 15rem;
    }
    #vbPageNav > li.page-item:last-child{
        right: 15rem;
    }
    <?php if(!empty($bookBackground)){ ?>
    html{
        background: url("../../media/book-background/<?php echo $UFolder."/".$bookBackground; ?>");
    }
<?php } ?>

/* BOOK TEMPLATE CSS */
*{
	padding: 0;
	margin: 0;
	font-family: sans-serif;
	box-sizing: content-box;
}
p{
	margin-bottom: 2px;
	font-size: 12px;
	text-align: justify;
}
body{
	overflow: hidden;
}
h1{
	font-size: 2rem;
}
.container, .book-content{
	display: flex;
	justify-content: center;
	align-items: center;
	max-width: 100%;
}

main.container-fluid{
    overflow-y: scroll;
    overflow-x: hidden;
    height: 1000px;
}

.paper-effect{
	width: 100%;
    max-width: 750px;
    padding-bottom: 100px;
}
body.book-open .custom-wrapper{
	top: 0;
    left: 0;
    padding: 0 12px;
    border-width: 3px 10px;
    border-style: solid;
    border-color: #ddd;
    position: relative;
}
div#vb-control-wrap{
    position: absolute;
    right: 5rem;
    top: -2rem;
}
.custom-wrapper:before{
	left: 0;
}
.custom-wrapper:after{
	right: 0;
}
body.book-open .custom-wrapper:before, body.book-open .custom-wrapper:after{
	position: absolute;
    top: 0;
    z-index: 10;
    width: 10px;
    height: 100%;
    background: -webkit-linear-gradient(left,#dddddd 33.33%,#f0f0f0 33.33%,#f0f0f0 66.66%,white 66.66%);
    background: linear-gradient(to right,#dddddd 33.33%,#f0f0f0 33.33%,#f0f0f0 66.66%,white 66.66%);
    background-size: 3px 100%;
    content: '';
    -webkit-backface-visibility: hidden;
    -moz-backface-visibility: hidden;
    -ms-backface-visibility: hidden;
    backface-visibility: hidden;
    z-index: 0;
    box-sizing: content-box;
}
/* .container{
	width: 100%;
	height: 100vh;
	background: url("../img/operation-theatre.jpg");
	background-position: top center;
    background-size: cover;
    background-repeat: no-repeat;
    overflow: hidden;
} */
.book-content{
	width: 65%;
	min-width: 250px;
	max-width: 380px;
	height: 580px;
	position: relative;
	perspective: 1000px;
    transition: 1s;
    right: -25%;
    overflow: hidden;
}
.book{
	position: absolute;
	width: 100%;
	height: 100%;
	transition: 1s;
	transform-style: preserve-3d;
	transform-origin: left; 
    background: white;
}
.page-number{
	position: absolute;
    bottom: 10px;
    left: 50%;
    z-index: 2;
}
.page-arrow-wrap > i{
	position: absolute;
    bottom: 10px;
    color: #5e5e5e;
    font-size: 1.5rem;
    z-index: 100;
}
.page-arrow-wrap > i.fa-arrow-circle-o-right{
	right:10px;
}
.page-arrow-wrap > i.fa-arrow-circle-o-left{
	left:10px;	
}
.face-front, .face-back{
	width: 100%;
	height: 100%;	
	box-sizing: border-box;
	overflow: hidden;
}
.face-front{
	border-top-left-radius: 3px;
	border-bottom-left-radius: 3px;
}
.face-back{
	position: absolute;
	top: 0;
	left: 0;
	/* transform: translateZ(-1px) rotateY(180deg); */
}
div.book>div.book-wrap>span.left-control, div.book>div.book-wrap>span.right-control{
	position: absolute;
    top: 0;
    z-index: 800;
    width: 50px;
    height: 100%;
    content: '';
    box-sizing: content-box;
    visibility: hidden;
}
div.book>div.book-wrap>span.left-control{
    left: 0;
    box-shadow: inset -20px 0 50px -20px rgba(0,0,0,.03);
}
div.book>div.book-wrap>span.right-control{
    right: 0;
    box-shadow: inset 20px 0 50px -20px rgba(0,0,0,.03);
}

div.book>div.book-wrap:hover span.left-control, div.book>div.book-wrap:hover span.right-control{
    visibility: visible;
}

div.book>div.book-wrap>span.left-control i{
    left: 25%;
} 

div.book>div.book-wrap>span.right-control i{
    right: 25%;
}

div.book>div.book-wrap>span.left-control i, div.book>div.book-wrap>span.right-control i{
    position: absolute;
    top: 50%;
    font-size: 2rem;
    opacity: .15;
    cursor: pointer;
}

.book-wrap{
	padding:2rem;
    height: 100%;
}
.book-wrap > h6{
	margin: .5rem 0;
}
.book-wrap ul > li{
	list-style: none;
	font-size: 12px;
}
#front-cover{
	background-image: url('../../media/bookcover/<?php echo "{$UFolder}/{$bookCoverFile}"; ?>');
}
#back-cover{
	background: url('../img/portadaBack.jpg');
}
#front-cover, #back-cover{
	background-size: 100% 100%;
}

h1.vb-book-main-title {
    vertical-align: middle;
    display: table-cell;
    width: 350px;
    height: 550px;
}

h1.vb-book-main-title > small {
    margin-top: 10px;
    font-style: italic;
    font-weight: 400;
    font-size: 1rem;
    display: block;
}

.text-center{
	text-align: center;
}

.px-5 {
    padding: 0 3rem!important;
}

div.rbook-template div.face-image{
    background-position: center!important;
    background-repeat: no-repeat!important;
    background-size: cover!important;
    height: 100%;
}

span.prt-pg-number{
    margin-top: -30px;
    margin-right: -15px;
}

div.vbChapter-wrap li.tbcLink{
    margin-bottom: .2rem;
}

/* Classes for Javascript use */

/* .trnsf{
	transform: translateX(375px);
}

div.book-content{
    transform: translateX(360px);
}

div.book-content.trnsf-reset{
	transform: translateX(180px);
} */

div#book-container div.ql-editor{
    padding:2rem 1rem!important;
}

/* @media para hacer el texto responsivo */

@media screen and (max-width: 800px){
	p{
		font-size: 12px;
	}
}




    
</style>
<script type="text/javascript">
jQuery(document).ready(function($){
	let wrap = $(".book-content");
	let pages = $(".book");
	let frontCover = $("#front-cover");
	let body = $("body");
	let parent;
	let front;
	let back;
	let page = 0;
	//console.log(pages.length);
	for(i=0; pages.length>i; i++){
		$(pages[i]).addClass("page-"+i);
		if(i !== 0){
			$(pages[i]).addClass("d-none");
		}
    }
    
    //PLAY SOUND
    const PlaySound = function(File,Dir,vol,Status){    
    
        if(Status === 0){
            let path = (Dir == 1) ? "users/<?php echo $UFolder; ?>/" : "";
            let Sound = $("#vb-audioplayer")[0];
            Sound.src='../../media/sounds/'+path+File;
            Sound.volume = vol;
            Sound.loop = true;
            $("span.sound-act").attr("data-status",0);
            return Sound; 
        }else{
            return null;
        }
        
    }

    const ProcessSound = function(){
        let active = $("span.sound-act");
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
            },delay*1000);
        }
    }

	$(document).on('click','.book-wrap span.right-control',function(){
		parent = $(this).parents(".book");
		front = $(this).attr("id");
		// if(front == "front-cover"){			
		// 	$(wrap).removeClass("trnsf-reset");
		// 	setTimeout(function(){
		// 		$("body").addClass("book-open");
		// 	},1000);
		// }
		if(page != pages.length-1){
			page++;
			$(".book").css("z-index","0");			
			//$(parent).css({'transform':'rotateY(-180deg)','z-index':'1'});			
            $(parent).removeClass("d-none");
            $('span.sound-data-holder.sound-act').removeClass('sound-act');   
            $(pages[page]).find('span.sound-data-holder').addClass('sound-act');         
			setTimeout(function(){				
                $(pages[page]).css("z-index",'2');
                if($('span.sound-data-holder.sound-act').data('sound') !== ''){
                    ProcessSound();
                }else{
                    $('span.sound-data-holder.sound-act').data('status',0);
                    ProcessSound();
                }
			},200);
            $(pages[page]).removeClass("d-none");            	
			page = (page>pages.length) ? pages.length-1 : page;
			for(i=0; pages.length>i; i++){
				if(i != page && i != page-1 && i != page-2){
					$(pages[i]).addClass("d-none");
				}
			}
			//console.log(page);
		}
	});

	$(document).on('click','.book-wrap span.left-control',function(){	
		if(page > 0){			
			back = $(this).attr("id");
			// if(back == "trsf"){			
			// 	$(wrap).addClass("trnsf-reset");				
			// 	$("body").removeClass("book-open");				
			// }
			$(".book").css("z-index","0");
			//$(pages[page-1]).css({"transform":"rotateY(0deg)","z-index":"2"});	
			$(pages[page-2]).removeClass("d-none");						
			setTimeout(function(){			
				$(pages[page]).css("z-index","1");	
				$(pages[page+1]).addClass("d-none");				
			},600);		
			page--;	
			page = (page<0) ? 0 : page;
			//console.log(page);
		}
	});

    //Load book pages in Table of contents
    setTimeout(function(){
        let partTitlePages = $('p.page-part-number');
        let chapterPages = $('p.page-chapter-number');
        let partPN;
        let partData;
        let chPN;
        let chPNData;
        //console.log('Part Title: '+partTitlePages.length, 'Chapter Page: '+chapterPages.length);
        for(i=0; i<partTitlePages.length; i++){
            partPN = $(partTitlePages[i]).text();
            partData = $(partTitlePages[i]).data('partpage');
            $('h5.part-page'+partData).append('<span class="float-right prt-pg-number"> '+partPN+'</span>');
            //console.log(partPN);
        }

        for(i=0; i<chapterPages.length; i++){
            chPN = $(chapterPages[i]).text();
            chPNData = $(chapterPages[i]).data('chapterpage');
            $('li.chapter-page'+chPNData).append('<span class="float-right"> '+chPN+'</span>');
            //console.log(chPN);
        }
    },1000)
});
</script>

<?php } ?>