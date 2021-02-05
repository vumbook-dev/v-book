<?php
require_once "../../config.php";
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
    $UFolder = DATAPATH;
    if(isset($_POST['chapter']) && isset($_POST['title']) && isset($_POST['id']) && isset($_POST['file']) && isset($_POST['template'])){
        $chapter = $_POST['chapter'];
        $title = $_POST['title'];
        $id = $_POST['id'];
        $file = $_POST['file'];
        $template = $_POST['template'];
        $list = file_get_contents("../../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
        $contentlist = json_decode($list);   

        $html = '<ul class="vb-contentlist-wrap pl-1 pr-3 pb-4">';
        foreach($contentlist as $key => $value){
            $cond = ($template == 'book') ? count($value->content) : strlen($value->content);
            if($value->chapter == $chapter){
                //$hide = ($value->id == 01) ? "d-none" : "d-flex";
                $hide = "d-flex";
                if($cond > 0){                    
                    //$viewbtn = '<button class="d-inline-block btn btn-secondary edit-vb-style px-2 py-1" data-key="'.$key.'" data-chapter="'.$chapter.'">Edit Style</button>';
                    $viewbtn = '<span class="vb-view-content" data-title="'.$value->cpart.'" data-chapter="'.$chapter.'" data-key="'.$key.'"><i class="fa fa-eye text-primary" aria-hidden="true"></i></span>';
                }else{ $viewbtn = ""; }
                $button = ($cond > 0)? '<i class="fa fa-pencil text-muted" data-status="1" aria-hidden="true"></i>' : '<i class="fa fa-plus text-success" data-status="0" aria-hidden="true"></i>';
                $html .= '<li class="list-item-vbcontent '.$hide.' justify-content-between align-items-center"><span class="vb-cnt-title" data-ch="'.$value->chapter.'" data-sctn="'.$key.'"><span class="editable-section pr-2 d-none d-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>'.$value->cpart.'</span>
                <div class="float-right d-none sceditable-btn"><button class="btn btn-danger py-1 px-2">Cancel</button>
                <button class="btn btn-primary py-1 px-2">Save</button></div><span class="vb-btn-wrap"><span class="vb-edit-style showing-lightbox" data-chapter="'.$chapter.'" data-key="'.$key.'" data-name="'.$file.'">'.$button.'</span><span class="vb-dlt-content text-light" data-chapter="'.$chapter.'" data-key="'.$key.'"><i class="fa fa-trash-o text-danger" aria-hidden="true"></i></span></span></li>';
            }
        }

        $html .= '<li class="list-item-vbcontent text-right"><span class="show-section-field"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add New Chapter</span></li>';
        
        $html .= '</ul>';

        echo $html;


    }
}