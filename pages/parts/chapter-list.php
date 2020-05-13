<?php
if(isset($_POST['key'])){
    $k = $_POST['key'];
    $list = file_get_contents("../../json/books-list-title.json");
    $book = json_decode($list);

    $chapterlist = $book[$k]->chapter;    
    //print_r($chapters);

    $html = '<ul class="chapter-list-group">';
    foreach($chapterlist as $key => $chapter){
        $x = json_decode($chapterlist[$key]);
        $html .= '<li class="list-item-vbtitle d-flex justify-content-between align-items-center">'.$x->name.'</li>';
    }
    $html .= '</ul>';

    echo $html;
}