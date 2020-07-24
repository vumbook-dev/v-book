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
        $bookData = json_decode($listChapter);
        $chapterData = $bookData[$book]->chapter;
        $chapter = json_decode($chapterData[$ch]);
        $chName = $chapter->name;
        $subtitle = (strlen($bookData[$book]->subtitle) > 0) ? "<small class='d-block h6'>{$bookData[$book]->subtitle}</small>" : "";
        $mainTitle = "{$bookData[$book]->title} {$subtitle}";
    }
    
    $content = file_get_contents("../../json/book-content/{$file}.json");
    $content = json_decode($content); 

    ?>
    
    

<div class="px-4 py-5 mt-3 <?php echo "text-{$content[$section]->align}"; ?>">
    <?php 
    if($ch == 0 && $section == 0){
        echo '<h1 class="vb-book-main-title text-center p-5">'.$mainTitle.'</h1>';
    }else{
    ?>
    <h3 class="vb-ch-title px-4 mb-5"><?php echo $mainTitle; ?></h3>
    <h4 class="px-4"><?php  echo (($ch != 0 && $section != 1) || ($ch != 0 && $section != 2)) ? $chName : ""; ?></h4>
    <h5 class="px-4 mb-5"><?php echo $content[$section]->cpart; ?></h5>
    <div id="style-bookview" class="py-2 px-4">
        <?php echo $content[$section]->content; ?>
    </div>
</div>

<?php 
}
}