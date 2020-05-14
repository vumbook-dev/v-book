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

            $chapter = $_POST['chapter'];
            $newChapter = array("name" => $chapter, "type" => 1);
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