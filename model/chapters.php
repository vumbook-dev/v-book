<?php
require_once "../config.php";
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
    $UFolder = DATAPATH;
    if(isset($_POST['action'])){
        $action = $_POST['action'];
        if($action == "add"){
            
            //ADD NEW CHAPTER
            if(isset($_POST['chapter']) && isset($_POST['key']) && isset($_POST['file'])){
                $file = $_POST['file'];
                $path = "../json/users/bookdata/{$UFolder}/";
                $list = file_get_contents("{$path}book-chapter/{$file}.json");
                $booklist = file_get_contents("{$path}books-list-title.json");
                $booklist = json_decode($booklist);
                $chapterlist = json_decode($list);
                $key = $_POST['key'];
                $book = $booklist[$key];
                $chapterArray = $book->chapter;

                $xchapter = $_POST['chapter'];
                $chapter = explode("{", $xchapter);
                $ichapter = (count($chapter) == 1) ? $chapter[0] : "$chapter[0] <small class='vb-content-subtitle h6'>". rtrim($chapter[1],"}") ."</small>";
                $newChapter = array("name" => $ichapter, "bgType" => "color", "background" => "#fff");
                $chapterlist[] = $newChapter;
                $newlist = json_encode($chapterlist);
                file_put_contents("{$path}book-chapter/{$file}.json",$newlist);     
                
                $nChapter = array("name" => $ichapter);
                $nChapter = json_encode($nChapter);
                $chapterArray[] = $nChapter;
                $booklist[$key]->chapter = $chapterArray;
                $newlist = json_encode($booklist);
                file_put_contents("{$path}books-list-title.json",$newlist);  
                
                //ADD NEW CONTENT DATA
                $title = $ichapter;
                $newCH = count($chapterlist);
                $json = array("title" => $title, "file" => $file, "chapter" => $newCH, "bookkey" => $key, "index" => $key );
                $json = json_encode($json);

                echo $json;
                die();           
            }else{
                die();
            }
        }elseif($action == "delete"){

            //DELETE CHAPTER
            if(isset($_POST['chapter']) && isset($_POST['key']) && isset($_POST['title']) && isset($_POST['file'])){
                $title = $_POST['title'];
                $file = $_POST['file'];
                $path = "../json/users/bookdata/{$UFolder}/";
                $list = file_get_contents("{$path}book-chapter/{$file}.json");
                $list = json_decode($list);
                $booklist = file_get_contents("{$path}books-list-title.json");
                $booklist = json_decode($booklist);
                $chapter = $_POST['chapter'];
                $key = $_POST['key'];                
                unset($list[$chapter]);
                $chapters = array_values($list);

                $book = $booklist[$key];  
                $chters = $book->chapter;
                unset($chters[$chapter]);
                $chters = array_values($chters);          
                $booklist[$key]->chapter = $chters;

                $nlist = json_encode($booklist);
                file_put_contents("{$path}books-list-title.json",$nlist);   

                $newlist = json_encode($list);
                file_put_contents("{$path}book-chapter/{$file}.json",$newlist);             

                echo $title." is Successfully Deleted.";
                die();           
            }else{
                die();
            }

        }elseif($action == "update"){
            $file = $_POST['file'];
            $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json");
            $chapters = json_decode($list,true);
            $bk = $_POST['book'];
            $ch = $_POST['chapter'];
            $content = $_POST['content'];
            if($content != $chapters[$ch]['name']){
                $chapters[$ch]['name'] = $content;
                $newUpdate = json_encode($chapters);
                file_put_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json",$newUpdate); 

                $arry = array("message" => 'Book Part Successfully Updated', "status" => "success");
                $arry = json_encode($arry);
                echo $arry;
                die();
            }else{
                $arry = array("message" => 'Book Part Successfully Updated', "status" => "success");
                $arry = json_encode($arry);
                echo $arry;
                die();
            }
        }elseif($action == "update_sound"){
            if(!empty($_POST['sound']) && !empty($_POST['volume']) && !empty($_POST['delay'])){
                $file = $_POST['file'];
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json");
                $chapters = json_decode($list,true);
                $bk = $_POST['book'];
                $ch = $_POST['chapter'];
                $sound = $_POST['sound'];
                $volume = $_POST['volume'];    
                $delay = $_POST['delay']; 

                $chapters[$ch]['sound'] = $sound;
                $chapters[$ch]['volume'] = $volume;
                $chapters[$ch]['delay'] = $delay;

                $newUpdate = json_encode($chapters);
                file_put_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json",$newUpdate); 

                $message = 'Sound Successfully Updated.';
                $status = "success";
                $arry = array("message" => $message, "status" => $status, "filepath" => $UFolder);
                $arry = json_encode($arry);
                echo $arry;
                die();                
            }else{
                die("Empty");
            }
        }elseif($action == "update_color"){
            if(!empty($_POST['color'])){
                $file = $_POST['file'];
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json");
                $chapters = json_decode($list,true);
                $bk = $_POST['book'];
                $ch = $_POST['chapter'];
                $color = $_POST['color'];
                if(!empty($color)){
                    $chapters[$ch]['bgType'] = "color";
                    $chapters[$ch]['background'] = $color;
                    $newUpdate = json_encode($chapters);
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json",$newUpdate); 

                    $message = 'Background Color Successfully Updated.';
                    $status = "success";
                    $arry = array("message" => $message, "status" => $status, "filepath" => $UFolder);
                    $arry = json_encode($arry);
                    echo $arry;                             
                }    
                die();   
            }else{
                die();
            }
        }elseif($action == "update_title"){
            if(isset($_POST['title']) && isset($_POST['book']) && isset($_POST['file']) && isset($_POST['chapter'])){
                $key = $_POST['book'];
                $ch = $_POST['chapter'];

                $fullTitle = $_POST['title'];
                $fullTitle = str_replace("{","<small class='vb-content-subtitle h6'>",$fullTitle);
                $fullTitle = str_replace("}","</small>",$fullTitle);                

                $path = "../json/users/bookdata/{$UFolder}/";
                $booklist = file_get_contents("{$path}books-list-title.json");
                $booklist = json_decode($booklist);
                $book = $booklist[$key];
                $chapters = $book->chapter;
                $newChapter = array("name" => $fullTitle);
                
                $newChapter = json_encode($newChapter);
                $chapters[$ch] = $newChapter;
                $chapters = array_values($chapters);
                $booklist[$key]->chapter = $chapters;
                $newlist = json_encode($booklist);
                file_put_contents("{$path}books-list-title.json",$newlist);    
                echo "Chapter Updated to \"{$fullTitle}\"";             
                die();           
            }else{
                die();
            }
        }
    }
}
?>