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

    }elseif($action == "update"){
        $list = file_get_contents("../json/books-list-title.json");
        $booklist = json_decode($list);
        $bk = $_POST['book'];
        $ch = $_POST['chapter'];
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $fullTitle = "{$title} <small class='vb-content-subtitle h6'>{$subtitle}</small>";
        $sound = $_POST['sound'];
        $volume = $_POST['volume'];    
        $color = $_POST['color'];  

        $book = $booklist[$bk];             
        $chapters = $book->chapter;
        $chInfo = json_decode($chapters[$ch]);
        $newChapter = array();
        ($fullTitle != $chInfo->name) ? $newChapter['name'] = $fullTitle : $newChapter['name'] = $chInfo->name;
        if(!empty($color)){
            $newChapter['bgType'] = "color";
            $newChapter['background'] = $color;
        }else{
            $newChapter['bgType'] = $chInfo->bgType;
            $newChapter['background'] = $chInfo->background;
        }    
        if(!empty($sound) && !empty($volume)){
            $newChapter['sound'] = $sound;
            $newChapter['volume'] = $volume;
        }
        $newChapter = json_encode($newChapter);
        $chapters[$ch] = $newChapter;
        $chapters = array_values($chapters);
        $booklist[$bk]->chapter = $chapters;
        $newUpdate = json_encode($booklist);
        file_put_contents("../json/books-list-title.json",$newUpdate); 

        $message = '<i class="fa fa-check-circle-o" aria-hidden="true"></i> Chapter Successfully Updated';
        $status = "success";
        $arry = array("message" => $message, "status" => $status);
        $arry = json_encode($arry);
        echo $arry;
    }
}
?>