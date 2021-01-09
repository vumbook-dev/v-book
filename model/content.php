<?php
require_once "../config.php";
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
    $UFolder = DATAPATH;
    if(isset($_POST['action'])){
        $action = $_POST['action'];
        if($action == "add"){
            //ADD CONTENT CHAPTER
            if(isset($_POST['id']) && isset($_POST['chapter']) && isset($_POST['title']) && isset($_POST['name']) && isset($_POST['index']) && isset($_POST['template'])){
                $id = $_POST['id'];
                $index = $_POST['index'];
                $template = $_POST['template'];
                $contentArray = ($template == 'book') ? [] : "";
                $bookData = file_get_contents("../json/users/bookdata/{$UFolder}/books-list-title.json");
                $bookData = json_decode($bookData);
                $default = $bookData[$index];
                $chapter = $_POST['chapter'];
                $title = $_POST['title'];
                $hshtitle = $default->storage;
                $name = $_POST['name'];

                $file = "{$hshtitle}-".substr($id,-6);
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                $contentlist = json_decode($list);      
                $ACC = 0;      

                //COUNT ALL ARRAY ON CURRENT CHAPTER AND REARRANGE
                foreach($contentlist as $key => $value){
                    $ACC = ($value->chapter == $chapter) ? $ACC+1 : $ACC;
                }
                $id = "$chapter".$ACC;
                $newContent = array("id" => $id,"chapter" => $chapter, "cpart" => $name, "sound" => $default->dsound, "volume" => 0.5, "delay" => 1, "content" => $contentArray, "bgType" => "color", "background" => "#fff");
                $contentlist[] = $newContent;
                $ARR = array_column($contentlist, 'id');
                array_multisort($ARR, SORT_ASC, $contentlist);

                //SAVE CONTENT DATA          
                $count = count($contentlist);
                $json = json_encode($contentlist);
                file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                //print_r($bookData);
                echo $count;
            }
        }elseif($action == "btmp_add"){
            if(isset($_POST['chapter']) && isset($_POST['file'])){
                $chapter = $_POST['chapter'];
                $file = $_POST['file'];
                $id = (isset($_POST['id'])) ? intval($_POST['id']) : "";
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                $contentlist = json_decode($list);
                $count = count($contentlist[$chapter]->content);
                $newID = (!empty($id)) ? intval($id+1) : $count+1;
                $content = array("id" => $newID, "text" => "");
                $content = json_encode($content);
                $contentlist[$chapter]->content[] = $content;
                $json = json_encode($contentlist);
                file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                echo $newID;
            }
        }elseif($action == "btmp_delete"){
            if(isset($_POST['chapter']) && isset($_POST['file']) && isset($_POST['key'])){
                $chapter = $_POST['chapter'];
                $file = $_POST['file'];
                $key = $_POST['key'];
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                $contentlist = json_decode($list);
                unset($contentlist[$chapter]->content[$key]);
                $contentlist[$chapter]->content = array_values($contentlist[$chapter]->content);
                $count = count($contentlist[$chapter]->content);
                $json = json_encode($contentlist);
                file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                echo $count;
            }
        }elseif($action == "btmp_update"){
            if(isset($_POST['chapter']) && isset($_POST['file']) && isset($_POST['key']) && isset($_POST['content'])){
                $chapter = $_POST['chapter'];
                $file = $_POST['file'];
                $key = $_POST['key'];
                $content = $_POST['content'];
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                $contentlist = json_decode($list);
                $oldContent = $contentlist[$chapter]->content[$key];
                $oldContent = json_decode($oldContent,true);
                $oldContent['text'] = $content;
                $oldContent = json_encode($oldContent);
                $contentlist[$chapter]->content[$key] = $oldContent;
                $json = json_encode($contentlist);
                file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
            }
        }elseif($action == "update_sound"){
            //UPDATE SOUND CHAPTER
            if(isset($_POST['file']) && isset($_POST['sound']) && isset($_POST['key'])){
                $file = $_POST['file'];
                $sound = $_POST['sound'];
                $volume = $_POST['volume'];
                $delay = $_POST['delay'];
                $key = $_POST['key'];              
                $allData = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");        
                $section = json_decode($allData);

                //SAVE DATA
                if(!empty($sound) || is_numeric($sound)){

                    //SAVE SOUND
                    if($section[$key]->sound !== $sound){
                        $section[$key]->sound = "{$sound}"; 
                    }                  
                    //SAVE SOUND VOLUME
                    if($section[$key]->volume != $volume){
                        $section[$key]->volume = $volume;
                    }

                    //SAVE SOUND VOLUME
                    if($section[$key]->delay != $delay){
                        $section[$key]->delay = $delay;
                    }
                                            
                    $json = json_encode($section);
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);

                    $message = '<i class="fa fa-check-circle-o" aria-hidden="true"></i> Content Successfully Updated';
                    $status = "success";
                }else{
                    $message = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Something Went Wrong</i>';
                    $status = "failed";
                }

                $arry = array("message" => $message, "status" => $status);
                $arry = json_encode($arry);
                echo $arry;
            
            }
        }elseif($action == "update_color"){
            //UPDATE SOUND CHAPTER
            if(isset($_POST['file']) && isset($_POST['color']) && isset($_POST['key'])){
                $file = $_POST['file'];
                $color = (isset($_POST['color'])) ? $_POST['color'] : "";
                $key = $_POST['key'];              
                $allData = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");        
                $section = json_decode($allData);

                //SAVE DATA
                if(!empty($color)){

                    if($section[$key]->background != $color){
                        $section[$key]->background = $color;
                        $section[$key]->bgType = "color";
                    }
                                            
                    $json = json_encode($section);
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);

                    $message = 'Content Successfully Updated';
                    $status = "success";
                }else{
                    $message = 'Something Went Wrong</i>';
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

                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                $content = json_decode($list);
                unset($content[$key]);
                $content = array_values($content);

                $json = json_encode($content);
                file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                echo "Deleted Successfully";
            }
            
        }elseif($action == "load"){
            if(isset($_POST['file']) && isset($_POST['key'])){
                $key = $_POST['key'];
                $file = $_POST['file'];

                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                $content = json_decode($list);
                $content = $content[$key];
                //$response = json_encode($content);
                echo $content->content;
            }
        }elseif($action == "update_title"){
            if(isset($_POST['title']) && isset($_POST['book']) && isset($_POST['file']) && isset($_POST['chapter']) && isset($_POST['section'])){
                $key = $_POST['section'];
                $file = $_POST['file'];
                $fullTitle = $_POST['title'];
                $allData = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");        
                $section = json_decode($allData);

                $section[$key]->cpart = $fullTitle;                        
                $json = json_encode($section);
                file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                echo "Section Updated to \"{$fullTitle}\"";
            }
        }
    }
}