<?php
require_once "../config.php";
require_once "./debug_helper.php";
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
    $UFolder = DATAPATH;

    //CREATE BACKUP FILES
    function createBackup($filename,$bookData,$folder){
        $list = file_get_contents("../json/users/bookdata/{$folder}/book-content/backup/backup-data.json");
        $backup = json_decode($list,true);
        $time = date("F j, Y, g:i a");
        $integerTime = str_replace(" ","-",date("Y-m-d H-i-s"));
        $skey = 0;
        foreach($backup as $f => $val){
            if(!empty($backup[$f][$filename])){
                $skey = ($val[$filename]['id'] === $filename) ? $f : 0;
            }            
        }
        if(!is_dir("../json/users/bookdata/{$folder}/book-content/backup/{$filename}")){
            mkdir("../json/users/bookdata/{$folder}/book-content/backup/{$filename}");            
        }
        if((!empty($backup[$skey][$filename])) ? $backup[$skey][$filename]['id'] === $filename : false){                      
            $seconds = strtotime(date("Y-m-d H:i:s")) - strtotime($backup[$skey][$filename]['time']);
            $days    = floor($seconds / 86400);
            $hours   = floor(($seconds - ($days * 86400)) / 3600);
            $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
            $timePassed = ($days > 0) ? ($days*24+$hours)*60+$minutes : $hours*60+$minutes;  
            if($timePassed < 30){
                return false;
            }else{                  
                $backup[$skey][$filename]['time'] = $time;
                $newBackup = json_encode($backup);                
                file_put_contents("../json/users/bookdata/{$folder}/book-content/backup/backup-data.json",$newBackup);
                file_put_contents("../json/users/bookdata/{$folder}/book-content/backup/{$filename}/{$integerTime}backup-{$filename}.json",$bookData);
                return true;                       
            }
        }else{
            $backup[] = array( $filename => ["id" => $filename,"time" => $time] );
            $newBackup = json_encode($backup);
            file_put_contents("../json/users/bookdata/{$folder}/book-content/backup/backup-data.json",$newBackup);
            file_put_contents("../json/users/bookdata/{$folder}/book-content/backup/{$filename}/{$integerTime}backup-{$filename}.json",$bookData);
            return true;
        }
    }

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
                $file = $default->storage;
                $name = $_POST['name'];
                
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                createBackup($file,$list,$UFolder);
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
                $helper = new Helper(FAILSAFE_DEBUG_MODE);
                $helper->failSafe($json,81,"content.php");
                if($helper->result){
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json, "count" => $count);
                }else{
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "count" => $count);                  
                }       
                echo json_encode($msg);         
                die();
            }
        }elseif($action == "btmp_add"){
            if(isset($_POST['chapter']) && isset($_POST['file'])){
                $chapter = $_POST['chapter'];
                $file = $_POST['file'];
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                createBackup($file,$list,$UFolder);
                $contentlist = json_decode($list);
                $count = count($contentlist[$chapter]->content);
                $newID = $count;
                $content = array("id" => $newID, "text" => "");
                $content = json_encode($content);
                $contentlist[$chapter]->content[] = $content;
                $json = json_encode($contentlist);           
                $helper = new Helper(FAILSAFE_DEBUG_MODE);
                $helper->failSafe($json,106,"content.php");
                if($helper->result){
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json, "newID" => $newID);
                }else{
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "newID" => $newID);                  
                }       
                echo json_encode($msg);         
                die();
            }
        }elseif($action == "btmp_delete"){
            if(isset($_POST['chapter']) && isset($_POST['file']) && isset($_POST['key'])){
                $chapter = $_POST['chapter'];
                $file = $_POST['file'];
                $key = $_POST['key'];
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                createBackup($file,$list,$UFolder);
                $contentlist = json_decode($list);
                unset($contentlist[$chapter]->content[$key]);
                $contentlist[$chapter]->content = array_values($contentlist[$chapter]->content);
                $count = count($contentlist[$chapter]->content);
                $json = json_encode($contentlist);            
                $helper = new Helper(FAILSAFE_DEBUG_MODE);
                $helper->failSafe($json,81,"content.php");
                if($helper->result){
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json, "newID" => $count);
                }else{
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "newID" => $count);                  
                }       
                echo json_encode($msg);         
                die();
            }
        }elseif($action == "btmp_update"){
            if(isset($_POST['chapter']) && isset($_POST['file']) && isset($_POST['key']) && isset($_POST['content'])){
                $chapter = $_POST['chapter'];
                $file = $_POST['file'];
                $key = $_POST['key'];
                $content = $_POST['content'];
                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                createBackup($file,$list,$UFolder);
                $contentlist = json_decode($list);
                $oldContent = $contentlist[$chapter]->content[$key];                
                $oldContent = json_decode($oldContent,true);
                $oldContent['text'] = $content;
                $oldContent = json_encode($oldContent);
                $contentlist[$chapter]->content[$key] = $oldContent;
                $json = json_encode($contentlist);
                $helper = new Helper(FAILSAFE_DEBUG_MODE);
                $helper->failSafe($json,156,"content.php");
                if($helper->result){
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json, "newID" => $key);
                }else{
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "newID" => $key);                  
                }       
                echo json_encode($msg);         
                die();
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
                createBackup($file,$allData,$UFolder);     
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
                    $helper = new Helper(FAILSAFE_DEBUG_MODE);
                    $helper->failSafe($json,197,"content.php");
                    if($helper->result){
                        $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json);
                    }else{
                        file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                        $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "flashMSG" => 'Sound Successfully Updated.');                  
                    } 
                }else{
                    $msg = array("mode" => "debug_disable", "flashMSG" => ' Something Went Wrong!', "errorType" => "danger");
                }
                echo json_encode($msg);         
                die();           
            }
        }elseif($action == "update_color"){
            //UPDATE SOUND CHAPTER
            if(isset($_POST['file']) && isset($_POST['color']) && isset($_POST['key'])){
                $file = $_POST['file'];
                $color = (isset($_POST['color'])) ? $_POST['color'] : "";
                $key = $_POST['key'];              
                $allData = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");    
                createBackup($file,$allData,$UFolder);    
                $section = json_decode($allData);

                //SAVE DATA
                if(!empty($color)){

                    if($section[$key]->background != $color){
                        $section[$key]->background = $color;
                        $section[$key]->bgType = "color";
                    }
                                            
                    $json = json_encode($section);    
                    $helper = new Helper(FAILSAFE_DEBUG_MODE);
                    $helper->failSafe($json,230,"content.php");
                    if($helper->result){
                        $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json);
                    }else{
                        file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                        $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "flashMSG" => 'Background Color Successfully Updated.');                  
                    }  
                }else{
                    $msg = array("mode" => "debug_disable", "flashMSG" => ' Something Went Wrong!', "errorType" => "danger");
                }

                echo json_encode($msg);         
                die();            
            }
        }elseif($action == "delete"){
            //DELETE CHAPTER CONTENT
            if(isset($_POST['key']) && isset($_POST['lctn'])){
                $key = $_POST['key'];
                $file = $_POST['lctn'];

                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
                createBackup($file,$list,$UFolder);
                $content = json_decode($list);
                unset($content[$key]);
                $content = array_values($content);

                $json = json_encode($content);                
                $helper = new Helper(FAILSAFE_DEBUG_MODE);
                $helper->failSafe($json,257,"content.php");
                if($helper->result){
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json);
                }else{
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "flashMSG" => "Deleted Successfully");                  
                }       
                echo json_encode($msg);         
                die();
            }
            
        }elseif($action == "load"){
            if(isset($_POST['file']) && isset($_POST['key'])){
                $key = $_POST['key'];
                $file = $_POST['file'];

                $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");                
                createBackup($file,$list,$UFolder);
                $content = json_decode($list);
                $content = $content[$key];
                //$response = json_encode($content);
                echo $content->content;
                die();
            }
        }elseif($action == "update_title"){
            if(isset($_POST['title']) && isset($_POST['book']) && isset($_POST['file']) && isset($_POST['chapter']) && isset($_POST['section'])){
                $key = $_POST['section'];
                $file = $_POST['file'];
                $fullTitle = $_POST['title'];
                $allData = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");     
                createBackup($file,$allData,$UFolder);   
                $section = json_decode($allData);
                $oldTitle = $section[$key]->cpart;

                $section[$key]->cpart = $fullTitle;                        
                $json = json_encode($section);                
                $helper = new Helper(FAILSAFE_DEBUG_MODE);
                $helper->failSafe($json,294,"content.php");
                if($helper->result){
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json);
                }else{
                    file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$json);
                    $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "flashMSG" => "Chapter Title: \"{$oldTitle}\" is successfully updated to \"{$fullTitle}\"");                  
                }       
                echo json_encode($msg);         
                die();
            }
        }
    }
}