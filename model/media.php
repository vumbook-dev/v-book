<?php
//DEFINE APP ROOT LINK
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
                file_put_contents("../json/users/bookdata/{$folder}/book-content/backup/{$integerTime}backup-{$filename}.json",$bookData);
                return true;              
            }
        }else{
            $backup[] = array( $filename => ["id" => $filename,"time" => $time] );
            $newBackup = json_encode($backup);
            file_put_contents("../json/users/bookdata/{$folder}/book-content/backup/backup-data.json",$newBackup);
            file_put_contents("../json/users/bookdata/{$folder}/book-content/backup/{$integerTime}backup-{$filename}.json",$bookData);
            return true;
        }
    }

    if(isset($_FILES['audio']) && $_FILES['audio']['name'] != ''){

        $media = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-sound.json");
        $media = json_decode($media);
        $og_count = count($media);
        $new_count = null;

        if(!is_dir("../media/sounds/users/{$UFolder}")){
            mkdir("../media/sounds/users/{$UFolder}");
        }


        foreach ($_FILES['audio']['name'] as $key => $value){
            $og_name = $_FILES['audio']['name'][$key];
            $file_name = explode(".", $_FILES['audio']['name'][$key]);
            $new_name = md5(rand()) . '.' . $file_name[1];  
            $new_name = "{$file_name[0]}-".substr($new_name,-11);
            $new_name = str_replace(" ","-",$new_name);
            $sourcePath = $_FILES['audio']['tmp_name'][$key];  
            $targetPath = "../media/sounds/users/{$UFolder}/".$new_name;  

            if(move_uploaded_file($sourcePath, $targetPath)){
                $new_count = $og_count + $key;
                $fileData = array("userID" => "u01", "filepath" => "{$UFolder}", "id" => "m{$new_count}", "alias" => $file_name[0], "filename" => $new_name);
                $media[] = $fileData;            
            }
        }

        if(is_numeric($new_count)){
            $json = json_encode($media);
            $newUpload = json_encode($fileData);
            file_put_contents("../json/users/bookdata/{$UFolder}/media/user-sound.json",$json);
            echo $newUpload;
            die();
        }

    }elseif(isset($_POST['action']) && isset($_POST['file']) && isset($_POST['key'])){
        $file = $_POST['file'];
        $key = $_POST['key'];
        $list = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
        $content = json_decode($list);

        $mysounds = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-sound.json");
        $mysounds = json_decode($mysounds);
                                        
        if(count($mysounds) > 0){
            echo '<ul class="mb-0 p-0 slct-sounds">';
            foreach($mysounds as $k => $value){
                $aliesname = (strlen($value->alias) > 25) ? substr($value->alias, 25) : $value->alias;
                $icon = '<i class="fa fa-play px-3" aria-hidden="true" data-dir="1" data-file="'.$value->filename.'"></i></li>';
                $activeSound = ($value->id === $content[$key]->sound) ? 'act-sound' : '';
                echo '<li class="slct-sounds-list '.$activeSound.'" data-id="'.$value->id.'">'.$value->alias.' '.$icon;
            }
            echo '</ul>';
            die();
        }else{
            echo '<span class="my-4 d-block">No Media Available!</span>';
            die();
        }
    }elseif(isset($_POST['section']) && $_FILES['background']['name'] != "" && isset($_POST['book']) && isset($_POST['file'])){
        //SAVE PART BACKGROUND IMAGE
        $media = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-background.json");
        $media = json_decode($media);
        $og_count = count($media);
        $new_count = null;

        $k = $_POST['section'];
        $bkey = $_POST['book'];
        $file = $_POST['file'];
        $allData = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");        
        createBackup($file,$allData,$UFolder);
        $section = json_decode($allData);        

        foreach ($_FILES['background']['name'] as $key => $value){
            $og_name = $_FILES['background']['name'][$key];
            $file_name = explode(".", $_FILES['background']['name'][$key]);
            $new_name = md5(rand()) . '.' . $file_name[1];  
            $new_name = "{$file_name[0]}-".substr($new_name,-11);
            $new_name = str_replace(" ","-",$new_name);
            $new_name = str_replace("(","",$new_name);
            $new_name = str_replace(")","",$new_name);
            $sourcePath = $_FILES['background']['tmp_name'][$key];  
            $targetPath = "../media/page-background/{$UFolder}/".$new_name;  

            if(move_uploaded_file($sourcePath, $targetPath)){
                $new_count = $og_count + $key;
                $fileData = array("id" => "m{$new_count}", "alias" => $file_name[0], "filename" => $new_name);
                $media[] = $fileData;            
            }
        }

        if(is_numeric($new_count)){
            $json = json_encode($media);
            file_put_contents("../json/users/bookdata/{$UFolder}/media/user-background.json",$json);
            $section[$k]->background = $new_name;
            $section[$k]->bgType = "image";
            $newUpdate = json_encode($section);            
            $helper = new Helper(FAILSAFE_DEBUG_MODE);
            $helper->failSafe($newUpdate,155,"media.php");            
            if($helper->result){
                $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $newUpdate);
            }else{
                file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$newUpdate);
                $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "flashMSG" => "Background Image Successfully Saved.");                  
            }       
            echo json_encode($msg);         
            die();
        }
    }elseif(isset($_POST['chapter']) && $_FILES['background']['name'] != "" && isset($_POST['book']) && isset($_POST['file'])){
        //SAVE CHAPTER BACKGROUND IMAGE
        $media = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-background.json");
        $media = json_decode($media);
        $og_count = count($media);
        $new_count = null;

        $k = $_POST['chapter'];
        $bkey = $_POST['book'];     
        $file = $_POST['file'];

        foreach ($_FILES['background']['name'] as $key => $value){
            $og_name = $_FILES['background']['name'][$key];
            $file_name = explode(".", $_FILES['background']['name'][$key]);
            $new_name = md5(rand()) . '.' . $file_name[1];  
            $new_name = "{$file_name[0]}-".substr($new_name,-11);
            $new_name = str_replace(" ","-",$new_name);
            $new_name = str_replace("(","",$new_name);
            $new_name = str_replace(")","",$new_name);
            $sourcePath = $_FILES['background']['tmp_name'][$key];  
            $targetPath = "../media/page-background/{$UFolder}/".$new_name;  

            if(move_uploaded_file($sourcePath, $targetPath)){
                $new_count = $og_count + $key;
                $fileData = array("id" => "m{$new_count}", "alias" => $file_name[0], "filename" => $new_name);
                $media[] = $fileData;            
            }
        }

        if(is_numeric($new_count)){
            $json = json_encode($media);
            file_put_contents("../json/users/bookdata/{$UFolder}/media/user-background.json",$json);                
            $chapterData = file_get_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json");
            $chapters = json_decode($chapterData,true);
            $chapters[$k]['bgType'] = "image";
            $chapters[$k]['background'] = $new_name;
            $newUpdate = json_encode($chapters);             
            $helper = new Helper(FAILSAFE_DEBUG_MODE);
            $helper->failSafe($newUpdate,155,"media.php");            
            if($helper->result){
                $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $newUpdate);
            }else{
                file_put_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json",$newUpdate);
                $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "flashMSG" => "Background Image Successfully Saved.");                  
            }       
            echo json_encode($msg);
            die();
        }
    }elseif(isset($_FILES['image']) && isset($_POST['dir'])){
        $dir = $_POST['dir'];
        $data = array();
        if(!is_dir("../media/images/users/{$UFolder}")){
            mkdir("../media/images/users/{$UFolder}");            
        }
        if(!is_dir("../media/images/users/{$UFolder}/".$dir)){
            mkdir("../media/images/users/{$UFolder}/".$dir); 
        }
        foreach ($_FILES['image'] as $key => $value){        
            $og_name = $_FILES['image']['name'][0];
            $type = $_FILES['image']['type'][0];
            $filename = explode("/", $type);
            $new_name = md5(rand()) . '.'.$filename[1];  
            $new_name = "{$filename[0]}-".substr($new_name,-11);
            $new_name = str_replace(" ","-",$new_name);
            $sourcePath = $_FILES['image']['tmp_name'][0];
            $targetPath = "../media/images/users/{$UFolder}/".$dir."/".$new_name;
            $savePath = URLROOT."media/images/users/{$UFolder}/".$dir."/".$new_name;

            if(move_uploaded_file($sourcePath, $targetPath)){
                $data["url"] = $savePath;
                $status["status"] = "success";
            }else{
                $status["status"] = "failed";
            }
            $alldata[] = $data;
            //print_r($_FILES['image'][0][$key]);
            //echo $_FILES['image'][$key];
        }
        //print_r($_FILES['image']);
        //echo $_FILES['image']['name'][0];
        $arry = json_encode($data);
        echo $arry;    
        die();
    }
}