<?php
if(isset($_POST['action'])){
    // function convertToVBPlayer($post){
    //     $args = array();
    //     $args[] = "/Dr. <\/span><span>/";
    //     $args[] = "/Mr. <\/span><span>/";
    //     $new = str_replace("<p>","<span> ",$post);
    //     $new = str_replace("</p>"," </span>",$new);
    //     $new = str_replace("<ol>","<span class='vb-textline'><ol>",$new);
    //     $new = str_replace("</ol>","</ol></span>",$new);
    //     $new = str_replace("<ul>","<span class='vb-textline'><ul>",$new);
    //     $new = str_replace("</ul>","</ul></span>",$new);
    //     $new = str_replace(",",", </span><span>",$new);
    //     $new = str_replace(".",". </span><span>",$new);
    //     $new = str_replace(":",": </span><span>",$new);
    //     $new = str_replace(";","; </span><span>",$new);
    //     $new = preg_replace($args,"Dr.",$new);
    //     return $new;
    // }
    $action = $_POST['action'];
    if($action == "add"){
        //ADD CONTENT CHAPTER
        if(isset($_POST['id']) && isset($_POST['chapter']) && isset($_POST['title']) && isset($_POST['name']) && isset($_POST['index'])){
            $id = $_POST['id'];
            $index = $_POST['index'];
            $bookData = file_get_contents("../json/books-list-title.json");
            $bookData = json_decode($bookData);
            $default = $bookData[$index];
            $chapter = $_POST['chapter'];
            $title = $_POST['title'];
            $hshtitle = str_replace(" ","-",$title);
            $name = $_POST['name'];

            $file = "{$hshtitle}-".substr($id,-6);
            $list = file_get_contents("../json/book-content/{$file}.json");
            $contentlist = json_decode($list);      
            $ACC = 0;      

            //COUNT ALL ARRAY ON CURRENT CHAPTER AND REARRANGE
            foreach($contentlist as $key => $value){
                $ACC = ($value->chapter == $chapter) ? $ACC+1 : $ACC;
            }
            $id = "$chapter".$ACC;
            $newContent = array("id" => $id,"chapter" => $chapter, "cpart" => $name, "sound" => $default->dsound, "content" => "");
            $contentlist[] = $newContent;
            $ARR = array_column($contentlist, 'id');
            array_multisort($ARR, SORT_ASC, $contentlist);

            //SAVE CONTENT DATA          
            $count = count($contentlist);
            $json = json_encode($contentlist);
            file_put_contents("../json/book-content/{$file}.json",$json);
            //print_r($bookData);
            echo $count;
        }
    }elseif($action == "update"){
        //UPDATE CONTENT CHAPTER
        if(isset($_POST['file']) && isset($_POST['sound']) && isset($_POST['content']) && isset($_POST['key'])){
            $file = $_POST['file'];
            $sound = $_POST['sound'];
            $color = $_POST['color'];
            $key = $_POST['key'];
            $ch = $_POST['chapter'];
            $content = $_POST['content'];
            $list = file_get_contents("../json/book-content/{$file}.json");            
            $contentlist = json_decode($list);

            //SAVE DATA
            if(!empty($sound) || is_numeric($sound)){
                $bookKey = $_POST['book'];
                $books = file_get_contents("../json/books-list-title.json");
                $booklist = json_decode($books);
                $contentlist[$key]->sound = "{$sound}";                     

                if(!empty($color)){
                    $book = $booklist[$bookKey];             
                    $chapters = $book->chapter;
                    $chInfo = json_decode($chapters[$ch]);
                    $newChapter = array("name" => $chInfo->name, "bgType" => "color", "background" => $color);
                    $newChapter = json_encode($newChapter);
                    $chapters[$ch] = $newChapter;
                    $chapters = array_values($chapters);
                    $booklist[$bookKey]->chapter = $chapters;                    
                }

                $newUpdate = json_encode($booklist);
                file_put_contents("../json/books-list-title.json",$newUpdate);


                // if(!empty($color)){
                //     if(empty($contentlist[$key]->background)){
                //         $contentlist[$key]["background"] = $new_name;
                //         $contentlist[$key]["bgType"] = "image";
                //     }else{
                //         $contentlist[$key]->background = $color;
                //         $contentlist[$key]->bgType = "color";
                //     }                    
                // }

                $contentlist[$key]->content = $content;                        
                $json = json_encode($contentlist);
                file_put_contents("../json/book-content/{$file}.json",$json);

                $message = '<i class="fa fa-check-circle-o" aria-hidden="true"></i> Content is Successfully Updated';
                $status = "success";
            }else{
                $message = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Something Went Wrong</i>';
                $status = "failed";
            }

            $arry = array("message" => $message, "status" => $status);
            $arry = json_encode($arry);
            echo $arry;
          
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