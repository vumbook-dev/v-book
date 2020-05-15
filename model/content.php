<?php
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if($action == "add"){
        //ADD CONTENT CHAPTER
        if(isset($_POST['id']) && isset($_POST['chapter']) && isset($_POST['title']) && isset($_POST['name'])){
            $id = $_POST['id'];
            $chapter = $_POST['chapter'];
            $title = $_POST['title'];
            $name = $_POST['name'];

            $file = "{$title}-".substr($id,-6);
            $list = file_get_contents("../json/book-content/{$file}.json");
            $contentlist = json_decode($list);

            $newContent = array("chapter" => $chapter, "cpart" => $name, "content" => "");
            $contentlist[] = $newContent;
            $json = json_encode($contentlist);
            file_put_contents("../json/book-content/{$file}.json",$json);
            echo "Added Successfully";
        }
    }elseif($action == "update"){
        //UPDATE CONTENT CHAPTER
        if(isset($_POST['file']) && isset($_POST['text']) && isset($_POST['key'])){
            $file = $_POST['file'];
            $text = $_POST['text'];
            $key = $_POST['key'];
            $list = file_get_contents("../json/book-content/{$file}.json");
            $contentlist = json_decode($list);
            $chapter = $contentlist[$key]->chapter;

            $contentlist[$key]->content = $text;
            $json = json_encode($contentlist);
            file_put_contents("../json/book-content/{$file}.json",$json);
            echo $chapter;
        }
    }elseif($action == "delete"){
        //DELETE CHAPTER CONTENT
        if(isset($_POST['key']) && isset($_POST['lctn'])){
            $key = $_POST['key'];
            $file = $_POST['lctn'];

            $list = file_get_contents("../json/book-content/{$file}.json");
            $content = json_decode($list);
            unset($content[$key]);
            $content = array_values($content);

            $json = json_encode($content);
            file_put_contents("../json/book-content/{$file}.json",$json);
            echo "Deleted Successfully";
        }
        
    }
}