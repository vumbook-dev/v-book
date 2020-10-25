<?php
require_once "../../function.php";
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
    $UFolder = "{$UName}{$UID}";
    if(isset($_POST['key']) && isset($_POST['data'])){        
        $k = $_POST['key'];
        $file = $_POST['data'];
        $book = file_get_contents("../../json/users/bookdata/{$UFolder}/books-list-title.json");
        $book = json_decode($book);
        //$list = file_get_contents("../../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json");
        $bookKey = $k + 1;
        //$chapters = json_decode($book[$bookKey]->chapter);
        $chapterlist = $book[$k]->chapter;    
        //print_r($chapters);

        $html = '<ul class="chapter-list-group" id="chapter-accordion">';
        foreach($chapterlist as $key => $chapter){
            $x = json_decode($chapterlist[$key]);
            //$y = ($key === 0) ? "Title Page" : $chapter['name'];
            $html .= '<li class="list-item-vbtitle d-flex justify-content-between align-items-center"><label for="Book Title" class="d-none text-right editable-label-title"><small>Put text inside <strong class="h4">{...}</strong> for book subtitle</small></label><h6 class="ttl-'.$key.'ch pl-3" data-ch="'.$key.'"><span class="editable-chtitle px-2 d-none d-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>'.$x->name;
            $html .= '</h6><div class="float-right d-none cheditable-btn" style="margin-top: -8px;"><button class="btn btn-danger py-1 px-2">Cancel</button>
            <button class="btn btn-primary py-1 px-2">Save</button></div><span><a class="vb-readview" href="/read/book='.$bookKey.'" target="_blank"><i class="fa fa-eye text-success" aria-hidden="true"></i></a><a class="px-3 ch-editor" data-ch="'.$key.'"><i class="fa fa-pencil text-secondary" aria-hidden="true"></i></a><i class="fa fa-trash-o text-danger vb-chapter-dlt" data-chapter="'.$key.'" aria-hidden="true"></i>';
            $html .= '<button type="button" class="btn btn-primary btn-chapter mx-2 d-none" data-chapter="'.$key.'" data-toggle="collapse" data-target="#'.$key.'chapter" aria-expanded="false" aria-controls="'.$key.'chapter">Show</button></span></li>';
            $html .= '<div class="collapse show" id="'.$key.'chapter" data-parent="#chapter-accordion"><div class="card card-body py-2">';
            $html .= '<div class="row"><div class="col-sm-12" id="vbcontent-list'.$key.'"></div>';
            $html .= contentForm($key,$book[$k]->id,$book[$k]->title,$k);
            $html .= '</div>';
            $html .= '</div></div>';
        }
        $html .= '</ul>';

        echo $html;
    }
}