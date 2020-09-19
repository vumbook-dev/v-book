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
            (count($chapter) == 1) ? $ichapter = $chapter[0] : $ichapter = "$chapter[0] <small class='vb-content-subtitle h6'>". rtrim($chapter[1],"}") ."</small>";
            $newChapter = array("name" => $ichapter, "bgType" => "color", "background" => "#fff");
            $newChapter = json_encode($newChapter);
            $chapterArray[] = $newChapter;
            $booklist[$key]->chapter = $chapterArray;
            //$_POST = array();
            $newlist = json_encode($booklist);
            file_put_contents("../json/books-list-title.json",$newlist);        
            
            //ADD NEW CONTENT DATA
            $title = $ichapter;
            $file = $book->storage;
            $bookkey = $book->id;
            $newCH = count($chapterArray);
            $json = array("title" => $title, "file" => $file, "chapter" => $newCH, "bookkey" => $bookkey, "index" => $key );
            $json = json_encode($json);

            echo $json;
        }
    }elseif($action == "delete"){

        //DELETE CHAPTER
        if(isset($_POST['chapter']) && isset($_POST['key']) && isset($_POST['title'])){
            $title = $_POST['title'];
            $list = file_get_contents("../json/books-list-title.json");
            $booklist = json_decode($list);
            $chapter = $_POST['chapter'];
            $key = $_POST['key'];
            $book = $booklist[$key];             
            $chapters = $book->chapter;
            unset($chapters[$chapter]);
            $chapters = array_values($chapters);
            //$newchapters = json_encode($chapters);             
            $booklist[$key]->chapter = $chapters;

            $newlist = json_encode($booklist);
            file_put_contents("../json/books-list-title.json",$newlist);             

            echo $title." is Successfully Deleted.";
        }

    }
}
?>