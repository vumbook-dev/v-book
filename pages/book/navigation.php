<?php
if(isset($_POST['book']) && isset($_POST['chapter']) && isset($_POST['section']) && isset($_POST['file'])){
    
    $book = $_POST['book'];
    $chapter = $_POST['chapter'];
    $section = $_POST['section'];
    $file = $_POST['file'];

    $listChapter = file_get_contents("../../json/books-list-title.json");
    $bookData = json_decode($listChapter);
    $chapterData = $bookData[$book]->chapter;
    $content = file_get_contents("../../json/book-content/{$file}.json");
    $content = json_decode($content);

    // foreach($content as $i => $value){
    //     if ($value->chapter !== $chapter) {
    //         unset($content[$key]);
    //     }
    // }

    ?>

<div class="card bg-light mx-3 mt-4 p-fixed" id="vbBookNavigationList">
  <div class="card-header text-center h5">Chapter</div>

    <?php 
    
    foreach($chapterData as $key => $bChapter){ 
        $x = json_decode($chapterData[$key]);
        ?>
            
            <div class="card-header p-0" id="heading<?php echo $key; ?>">
                <h5 class="mb-0">
                    <button style="width:100%" class="btn <?php echo ($key == 0) ? "collapsed" : ""; ?>" type="button" data-toggle="collapse" data-target="#collapse<?php echo $key; ?>" aria-expanded="true" aria-controls="collapse<?php echo $key; ?>">
                        <?php echo $x->name; ?> <i class="fa fa-angle-right px-3" aria-hidden="true" data-dir="default"></i>
                    </button>
                </h5>
            </div>

            <div id="collapse<?php echo $key; ?>" class="collapse <?php echo ($key == 0) ? "show" : ""; ?>" aria-labelledby="heading<?php echo $key; ?>" data-parent="#vbBookNavigationList">
                <div class="card-body p-0">
                    <ul class="mb-0 p-0 vb-section-list-nav">
                        <?php 
                            $i = 0;
                            foreach($content as $k => $value){
                                if($value->chapter == $key){
                                    $activeChapter = ($k == $section) ? 'act-section' : '';
                                    echo '<li class="'.$activeChapter.'" data-chapter="'.$key.'" data-section="'.$k.'">'.$value->cpart.'</li>';
                                }                        
                            }
                        ?>
                    </ul>
                </div>
            </div>
        

<?php 
    }     
?>

</div>

<?php

}