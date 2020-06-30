<?php
if(isset($_POST['chapter']) && isset($_POST['title']) && isset($_POST['id'])){
    $chapter = $_POST['chapter'];
    $title = $_POST['title'];
    $hshtitle = str_replace(" ","-",$title);
    $id = $_POST['id'];

    $file = "{$hshtitle}-".substr($id,-6);
    $list = file_get_contents("../../json/book-content/{$file}.json");
    $contentlist = json_decode($list);
    

    $html = '<ul class="vb-contentlist-wrap pl-1 pr-3 pb-4">';
    foreach($contentlist as $key => $value){
        if($value->chapter == $chapter){
            if(strlen($value->content) > 0){
                $viewbtn = '<button class="d-inline-block btn btn-secondary edit-vb-style px-2 py-1" data-key="'.$key.'" data-chapter="'.$chapter.'">Edit Style</button>';
                $viewbtn .= '<span class="vb-view-content" data-title="'.$value->cpart.'" data-chapter="'.$chapter.'" data-key="'.$key.'"><i class="fa fa-eye text-primary" aria-hidden="true"></i></span>';
            }else{ $viewbtn = ""; }
            $button = (strlen($value->content) > 0)? '<i class="fa fa-pencil text-muted" data-status="1" aria-hidden="true"></i>' : '<i class="fa fa-plus text-success" data-status="0" aria-hidden="true"></i>';
            $html .= '<li class="list-item-vbcontent d-flex justify-content-between align-items-center"><span class="vb-cnt-title">'.$value->cpart.'</span><span class="vb-btn-wrap">'.$viewbtn.'<span class="vb-dlt-content text-light" data-chapter="'.$chapter.'" data-key="'.$key.'"><i class="fa fa-trash-o text-danger" aria-hidden="true"></i></span><span class="vb-edit-content showing-lightbox" data-chapter="'.$chapter.'" data-key="'.$key.'" data-name="'.$file.'">'.$button.'</span></span></li>';
        }
    }

    //$html .= '<h5 class="text-center">No Content Available!</h5>';
    
    $html .= '</ul>';

    echo $html;


}