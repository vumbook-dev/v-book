<?php
require_once "../../function.php";
if(isset($_POST['key'])){
    $k = $_POST['key'];
    $list = file_get_contents("../../json/books-list-title.json");
    $book = json_decode($list);

    $chapterlist = $book[$k]->chapter;    
    //print_r($chapters);

    $html = '<ul class="chapter-list-group" id="chapter-accordion">';
    foreach($chapterlist as $key => $chapter){
        $x = json_decode($chapterlist[$key]);
        $html .= '<li class="list-item-vbtitle d-flex justify-content-between align-items-center"><h6>'.$x->name;
        $html .= '</h6><button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#'.$key.'chapter" aria-expanded="false" aria-controls="'.$key.'chapter">Show</button></li>';
        $html .= '<div class="collapse" id="'.$key.'chapter" data-parent="#chapter-accordion"><div class="card card-body">';
        $html .= '<div class="row"><div class="vbcontent-list col-sm-8"></div>';
        $html .= contentForm($key,$book[$k]->id,$key);
        $html .= '</div>';
        $html .= '</div></div>';
    }
    $html .= '</ul>';

    echo $html;
}