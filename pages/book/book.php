<?php
if(isset($_POST['book']) && isset($_POST['chapter']) && isset($_POST['section']) && isset($_POST['file'])){
    
    $book = $_POST['book'];
    $ch = 0;
    $section = $_POST['section'];
    $file = $_POST['file'];

    if(isset($_POST['chapters']) && isset($_POST['title']) && isset($_POST['subtitle'])){
        $chapters = $_POST['chapters'];
        $chapters = str_replace("\'",'"',$chapters);
        $chapters = str_replace("{",'<',$chapters);
        $chapters = str_replace("}",'>',$chapters);
        $chapters = explode('|,', $chapters);
        $chName = $chapters[$ch];
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $subtitle = (strlen($subtitle) > 0) ? "<small class='d-block h6'>{$subtitle}</small>" : "";
        $mainTitle = "{$title} {$subtitle}";
    }else{
        $listChapter = file_get_contents("../../json/books-list-title.json");
        $bookCover = file_get_contents("../../json/users/user-bookcover.json");
        $bookCover = json_decode($bookCover);
        $bookData = json_decode($listChapter);
        $coverKey = $bookData[$book]->cover;
        $chapterData = $bookData[$book]->chapter;
        $chapter = json_decode($chapterData[$ch]);        
        $subtitle = (strlen($bookData[$book]->subtitle) > 0) ? "<small class='d-block h6'>{$bookData[$book]->subtitle}</small>" : "";
        $title= $bookData[$book]->title;
        $mainTitle = "{$title} {$subtitle}";
    }
    
    $contents = file_get_contents("../../json/book-content/{$file}.json");
    $contents = json_decode($contents); 
    $dsound = file_get_contents("../../json/media/default-sounds.json");
    $dsound = json_decode($dsound);
    $msound = file_get_contents("../../json/users/user-sound.json");
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
    
<div id="bookWrapQuill">
    <?php 
    if($bookData[$book]->cover !== null){
        echo '<div id="page0" class="vbBookCover-wrap vbPages" data-chapter="0"><img src="/media/bookcover/user/'.$bookCover[$coverKey]->filename.'" alt="'.$title.'"></div>';
        $xpage = 4;
    }else{
        $xpage = 3;
    }
    $show1stpage = ($xpage === 4) ? "d-none" : "";
    $tb = $contents[0];
    $chBgType = (!empty($chapter->bgType)) ? $chapter->bgType : "color";
    $chBackground = (!empty($chapter->background)) ? $chapter->background : "#fff";

     ?>
    <?php echo '<div id="page1" class="'.$show1stpage.' vbPages vbPagesTitle" data-bgtype="'.$chBgType.'" data-background="'.$chBackground.'" data-status="0" data-sound="'.$sound.'" data-sdir="'.$dir.'"><h1 class="vb-book-main-title text-center p-5">'.$mainTitle.'</h1></div>'; ?>
    <div class="vbPage00 d-none vbPages vbPageContent" data-bgtype="<?php echo $tb->bgType; ?>" data-background="<?php echo $tb->background; ?>" data-chapter="0" id="page2" data-status="0" data-volume="<?php echo $volume; ?>" data-sound="<?php echo $sound; ?>" data-sdir="<?php echo $dir; ?>"></div>
    <div class="vbTBL-contents d-none vbPages vbPageContent" id="page3" data-bgtype="color" data-background="#fff">
    <h2 class="text-center">Contents</h2>
    <?php  
        
        foreach($chapterData as $key => $bChapter){             
            $x = json_decode($chapterData[$key]);
            $chapterName = $x->name; ?>
        <div class="vbChapter-wrap">
        <?php if($key != 0){
            echo "<h5 class='tbcLink' data-page='$xpage'>$chapterName</h5>";
            $xpage = $xpage+1;
        }?>
        <ul>
            <?php 
            
            foreach($contents as $k => $val){
                if($val->chapter == $key && $val->id != 00){
                    echo "<li class='tbcLink' data-page='$xpage'>$val->cpart</li>";
                    $xpage = $xpage+1;
                }                
            }
            
            ?>
        </ul>
        </div>
    <?php } ?>
    </div>
    <?php 
    $page = 3;
    $chx = "x";
    foreach($chapterData as $key => $bChapter){             
        $i = json_decode($chapterData[$key]);
        if($key != 0){               
            $chBgType = (!empty($i->bgType)) ? $i->bgType : "color";
            $chBackground = (!empty($i->background)) ? $i->background : "#fff"; 
            echo "<div class='d-none vbPages vbPagesTitle' id='page{$page}' data-bgtype='{$chBgType}' data-background='{$chBackground}'><h1 class='vb-book-main-title text-center px-2 py-5'>$i->name</h1></div>";
            $page = $page+1;
        }
        foreach($contents as $k => $value){        
            $chPtitle = $value->chapter;
            $volume = (!empty($contents[$k]->volume)) ? $contents[$k]->volume : 0.5;
            if($value->id != "00"){
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
                    echo "<div class='vbPage{$value->id} d-none vbPages vbPageContent' id='page{$page}'  data-bgtype='{$bgType}' data-background='{$bgVal}' data-status='0' data-volume='$volume' data-sound='$sound' data-sdir='$dir'></div>";
                }
                
            }
            $page = $page+1;
            $sound = "";
        }
    }
    ?>
</div>
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
        width: 380px;
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
    div#vb-control-wrap{
        position: absolute;
        right:0;
    }
</style>
<?php 
}