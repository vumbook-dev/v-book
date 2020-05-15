<?php
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if($action == "add"){
        
        //ADD NEW CHAPTER
        if(isset($_POST['chapter']) && isset($_POST['key'])){
            $list = file_get_contents("../json/books-list-title.json");
            $booklist = json_decode($list);
            $key = $_POST['key'];
            $book = $booklist[$key];
            $chapterArray = $book->chapter;

            $xchapter = $_POST['chapter'];
            $chapter = explode("{", $xchapter);
            (count($chapter) == 1) ? $ichapter = $chapter[0] : $ichapter = "$chapter[0] <small class='vb-content-subtitle'>". rtrim($chapter[1],"}") ."</small>";
            $newChapter = array("name" => $ichapter, "type" => 1);
            $newChapter = json_encode($newChapter);
            $chapterArray[] = $newChapter;
            $booklist[$key]->chapter = $chapterArray;
            //$_POST = array();
            $newlist = json_encode($booklist);
            file_put_contents("../json/books-list-title.json",$newlist);            
        }
    }
}
?>