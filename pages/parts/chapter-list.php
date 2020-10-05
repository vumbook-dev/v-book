<?php
require_once "../../function.php";
if(isset($_POST['key'])){
    $k = $_POST['key'];
    $list = file_get_contents("../../json/books-list-title.json");
    $bookKey = $k + 1;
    $book = json_decode($list);

    $chapterlist = $book[$k]->chapter;    
    //print_r($chapters);

    $html = '<ul class="chapter-list-group" id="chapter-accordion">';
    foreach($chapterlist as $key => $chapter){
        $x = json_decode($chapterlist[$key]);
        $y = ($key == 0) ? "(Title Page)" : "";
        $html .= '<li class="list-item-vbtitle d-flex justify-content-between align-items-center"><h6 class="ttl-'.$key.'ch">'.$x->name.' '.$y;
        $html .= '</h6><span><a class="vb-readview" href="/read/book='.$bookKey.'" target="_blank"><i class="fa fa-eye text-success" aria-hidden="true"></i></a><a class="px-3 ch-editor" data-ch="'.$key.'"><i class="fa fa-pencil text-secondary" aria-hidden="true"></i></a><button class="btn btn-danger vb-chapter-dlt" data-chapter="'.$key.'">Delete</button>';
        $html .= '<button type="button" class="btn btn-primary btn-chapter mx-2" data-chapter="'.$key.'" data-toggle="collapse" data-target="#'.$key.'chapter" aria-expanded="false" aria-controls="'.$key.'chapter">Show</button></span></li>';
        $html .= '<div class="collapse" id="'.$key.'chapter" data-parent="#chapter-accordion"><div class="card card-body">';
        $html .= '<div class="row"><div class="col-sm-8" id="vbcontent-list'.$key.'"></div>';
        $html .= contentForm($key,$book[$k]->id,$book[$k]->title,$k);
        $html .= '</div>';
        $html .= '</div></div>';
    }
    $html .= '</ul>';

    echo $html;
}