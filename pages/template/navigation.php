<?php
if(isset($_POST['book']) && isset($_POST['chapter']) && isset($_POST['section']) && isset($_POST['file'])){
    
    $book = $_POST['book'];
    $ch = $_POST['chapter'];
    $section = $_POST['section'];
    $file = $_POST['file'];

    $listChapter = file_get_contents("../../json/books-list-title.json");
    $bookData = json_decode($listChapter);
    $chapterData = $bookData[$book]->chapter;

    $content = file_get_contents("../../json/book-content/$file.json");
    $content = json_decode($content);
    $count = count($content);
    $dsound = file_get_contents("../../json/media/default-sounds.json");
    $dsound = json_decode($dsound);
    $msound = file_get_contents("../../json/users/user-sound.json");
    $msound = json_decode($msound);
?>

<div class="card bg-light" id="vbBookNavigationList">
  <div class="card-header text-right h5">Table of Contents <span class="x-close btn btn-secondary">Close</span></div>

    <?php $i = 0;
    
    foreach($chapterData as $key => $bChapter){ 
            $x = json_decode($chapterData[$key]);
            $chapterName = $x->name;
        
        ?>
            
            <div class="card-header p-0" id="heading<?php echo $key; ?>">
                <h5 class="mb-0">
                    <button style="width:100%" class="btn <?php echo ($key != 0) ? "collapsed" : ""; ?> text-right" type="button" data-toggle="collapse" data-target="#collapse<?php echo $key; ?>" aria-expanded="true" aria-controls="collapse<?php echo $key; ?>">
                        <?php echo $chapterName; ?><span class="marker"></span>
                    </button>
                </h5>
            </div>

            <div id="collapse<?php echo $key; ?>" class="collapse <?php echo ($key == 0) ? "show" : ""; ?>" aria-labelledby="heading<?php echo $key; ?>" data-parent="#vbBookNavigationList">
                <div class="card-body p-0">
                    <?php if($i < $count) { 
                                /*if($content[$i]->chapter != $key){
                                    echo '<div class="text-center p-5">No Section Available</div>';
                                } */?>
                        <ul class="mb-0 p-0 vb-section-list-nav">
                            <?php                                 
                                foreach($content as $k => $value){
                                    if($value->chapter == $key){
                                        $activeChapter = ($k == $section) ? 'act-section' : '';
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
                                        echo '<li class="'.$activeChapter.'" data-nav="'.$i.'" data-status="0" data-chapter="'.$key.'" data-section="'.$k.'" data-sound="'.$sound.'" data-sdir="'.$dir.'"><span class="marker"></span>'.$value->cpart.'</li>';
                                        $i++;
                                    }                        
                                }
                            ?>
                        </ul>
                     <?php } ?>
                </div>
            </div>
        

<?php } ?>

</div>

<?php

}