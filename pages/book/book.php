<?php
if(isset($_POST['book']) && isset($_POST['chapter']) && isset($_POST['section']) && isset($_POST['file'])){
    
    $book = $_POST['book'];
    $ch = $_POST['chapter'];
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
        $bgValue = ($bookData[$book]->bgType === "color") ? $bookData[$book]->bgValue : "url(../../media/background/".$bookData[$book]->bgValue.")" ;
    }
    
    $contents = file_get_contents("../../json/book-content/{$file}.json");
    $contents = json_decode($contents); 
    $dsound = file_get_contents("../../json/media/default-sounds.json");
    $dsound = json_decode($dsound);
    $msound = file_get_contents("../../json/users/user-sound.json");
    $msound = json_decode($msound);

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
    ?>
    
    

<div id="bookWrapQuill">
    <?php echo '<div id="page0" class="vbBookCover-wrap vbPages"><img src="/media/bookcover/user/'.$bookCover[$coverKey]->filename.'" alt="'.$title.'"></div>'; ?>
    <?php echo '<div id="page1" class="d-none vbPages" data-status="0" data-sound="'.$sound.'" data-sdir="'.$dir.'"><h1 class="vb-book-main-title text-center p-5">'.$mainTitle.'</h1></div>'; ?>
    <div class="vbPage00 d-none vbPages" id="page2" data-status="0" data-sound="<?php echo $sound; ?>" data-sdir="<?php echo $dir; ?>"></div>
    <div class="vbTBL-contents d-none vbPages" id="page3">
    <h2 class="text-center">Contents</h2>
    <?php  
        $xpage = 4;
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
            echo "<div class='d-none vbPages' id='page{$page}'><h1 class='vb-book-main-title text-center p-5'>$i->name</h1></div>";
            $page = $page+1;
        }
        foreach($contents as $k => $value){        
            $chPtitle = $value->chapter;
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
                    echo "<div class='vbPage{$value->id} d-none vbPages' id='page{$page}' data-status='0' data-sound='$sound' data-sdir='$dir'></div>";
                }
                
            }
            $page = $page+1;
            $sound = "";
        }
    }
    ?>
</div>
<style>
    div#book-container{
        background:<?php echo $bgValue; ?>
    }
    div.ql-container.ql-snow{
        border:0;
    }
    div.vbPages{
        padding: 2rem;
    }
    div.vbBookCover-wrap.vbPages{
        padding:0;
    }
</style>
<?php 
}