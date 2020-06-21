<?php
if(isset($_POST['action'])){
    function convertToVBPlayer($post){
        $args = array();
        $args[] = "/Dr. <\/span><span>/";
        $args[] = "/Mr. <\/span><span>/";
        $new = str_replace("<p>","<span> ",$post);
        $new = str_replace("</p>"," </span>",$new);
        $new = str_replace("<ol>","<span class='vb-textline'><ol>",$new);
        $new = str_replace("</ol>","</ol></span>",$new);
        $new = str_replace("<ul>","<span class='vb-textline'><ul>",$new);
        $new = str_replace("</ul>","</ul></span>",$new);
        $new = str_replace(",",", </span><span>",$new);
        $new = str_replace(".",". </span><span>",$new);
        $new = str_replace(":",": </span><span>",$new);
        $new = str_replace(";","; </span><span>",$new);
        $new = preg_replace($args,"Dr.",$new);
        return $new;
    }
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

            $newContent = array("chapter" => $chapter, "bg" => "", "cpart" => $name, "sound" => 0, "content" => "");
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
            //$args = "/Dr.<\/span><span>/";
            $text = convertToVBPlayer($text);
            $key = $_POST['key'];
            $list = file_get_contents("../json/book-content/{$file}.json");
            $contentlist = json_decode($list);
            $chapter = $contentlist[$key]->chapter;
            //SAVE DATA
            $contentlist[$key]->content = $text;
            $json = json_encode($contentlist);
            file_put_contents("../json/book-content/{$file}.json",$json);
            echo $chapter;
        }elseif(isset($_POST['file']) && isset($_POST['sound']) && isset($_POST['bg']) && isset($_POST['key'])){
            $file = $_POST['file'];
            $sound = $_POST['sound'];
            $bg = $_POST['bg'];
            $key = $_POST['key'];
            $list = file_get_contents("../json/book-content/{$file}.json");
            $contentlist = json_decode($list);
            //SAVE DATA
            if(is_numeric($sound) && is_numeric($bg)){
                $contentlist[$key]->sound = $sound;
                $contentlist[$key]->bg = $bg;
                $json = json_encode($contentlist);
                file_put_contents("../json/book-content/{$file}.json",$json);

                $message = 'Style is Successfully Updated <i class="fa fa-check" aria-hidden="true"></i>';
                $status = "success";
            }else{
                $message = 'Something Went Wrong! <i class="fa fa-times" aria-hidden="true"></i>';
                $status = "failed";
            }

            $arry = array("message" => $message, "status" => $status);
            $arry = json_encode($arry);
            echo $arry;
            //die();            
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