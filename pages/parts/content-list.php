<?php
if(isset($_POST['chapter']) && isset($_POST['title']) && isset($_POST['id'])){
    $chapter = $_POST['chapter'];
    $title = $_POST['title'];
    $id = $_POST['id'];

    $file = "{$title}-".substr($id,-6);
    $list = file_get_contents("../../json/book-content/{$file}.json");
    $contentlist = json_decode($list);

    $html = '<ul class="vb-contentlist-wrap pl-1 pr-3 pb-4">';
    foreach($contentlist as $key => $value){
        if($value->chapter == $chapter){
            $html .= '<li class="list-item-vbcontent d-flex justify-content-between align-items-center"><span>'.$value->cpart.'</span><span class="float-right" id="btn-content'.$key.'" data-chapter="'.$chapter.'" data-key="'.$key.'" data-name="'.$file.'"><i class="fa fa-plus text-success" aria-hidden="true"></i></span></li>';
        }
    }

    //$html .= '<h5 class="text-center">No Content Available!</h5>';
    
    $html .= '</ul>';

    echo $html;


}