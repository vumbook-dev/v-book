<?php
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
    $UFolder = "{$UName}{$UID}";
    if(isset($_FILES['audio']) && $_FILES['audio']['name'] != ''){

        $media = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-sound.json");
        $media = json_decode($media);
        $og_count = count($media);
        $new_count = null;


        foreach ($_FILES['audio']['name'] as $key => $value){
            $og_name = $_FILES['audio']['name'][$key];
            $file_name = explode(".", $_FILES['audio']['name'][$key]);
            $new_name = md5(rand()) . '.' . $file_name[1];  
            $new_name = "{$file_name[0]}-".substr($new_name,-11);
            $new_name = str_replace(" ","-",$new_name);
            $sourcePath = $_FILES['audio']['tmp_name'][$key];  
            $targetPath = "../media/sounds/user/".$new_name;  

            if(move_uploaded_file($sourcePath, $targetPath)){
                $new_count = $og_count + $key;
                $fileData = array("userID" => "u01", "id" => "m{$new_count}", "alias" => $file_name[0], "filename" => $new_name);
                $media[] = $fileData;            
            }
        }

        if(is_numeric($new_count)){
            $json = json_encode($media);
            $newUpload = json_encode($fileData);
            file_put_contents("../json/users/bookdata/{$UFolder}/media/user-sound.json",$json);
            echo $newUpload;
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
        }else{
            echo '<span class="my-4 d-block">No Media Available!</span>';
        }
    }elseif(isset($_POST['section']) && $_FILES['background']['name'] != "" && isset($_POST['book']) && isset($_POST['file'])){
        //SAVE SECTION BACKGROUND IMAGE
        $media = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-background.json");
        $media = json_decode($media);
        $og_count = count($media);
        $new_count = null;

        $k = $_POST['section'];
        $bkey = $_POST['book'];
        $file = $_POST['file'];
        $allData = file_get_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json");
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
            $targetPath = "../media/background/".$new_name;  

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
                
            // $bookData = file_get_contents("../json/books-list-title.json");
            // $booklist = json_decode($bookData); 
            // $book = $booklist[$bkey];             
            // $chapters = $book->chapter;
            // $chInfo = json_decode($chapters[$k]);
            // $newChapter = array("name" => $chInfo->name, "bgType" => "image", "background" => $new_name);
            // $newChapter = json_encode($newChapter);
            // $chapters[$k] = $newChapter;
            // $chapters = array_values($chapters);
            // $booklist[$bkey]->chapter = $chapters;
            // $newUpdate = json_encode($booklist);
            // file_put_contents("../json/books-list-title.json",$newUpdate); 
            $newUpdate = json_encode($section);     
            file_put_contents("../json/users/bookdata/{$UFolder}/book-content/{$file}.json",$newUpdate);  
            echo $new_name;

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
            $targetPath = "../media/background/".$new_name;  

            if(move_uploaded_file($sourcePath, $targetPath)){
                $new_count = $og_count + $key;
                $fileData = array("id" => "m{$new_count}", "alias" => $file_name[0], "filename" => $new_name);
                $media[] = $fileData;            
            }
        }

        if(is_numeric($new_count)){
            $json = json_encode($media);
            file_put_contents("../json/users/bookdata/{$UFolder}/media/user-background.json",$json);
            // $section[$k]->background = $new_name;
            // $section[$k]->bgType = "image";
                
            $chapterData = file_get_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json");
            $chapters = json_decode($chapterData,true);
            $newChapter = array("name" => $chapters[$k]['name'], "bgType" => "image", "background" => $new_name);
            $chapters[$k] = $newChapter;
            $chapters = array_values($chapters);
            $newUpdate = json_encode($chapters);
            file_put_contents("../json/users/bookdata/{$UFolder}/book-chapter/{$file}.json",$newUpdate);  
            echo $new_name;
        }
    }elseif(isset($_FILES['image']) && isset($_POST['dir'])){
        $dir = $_POST['dir'];
        $data = array();
        if(!is_dir("../media/user/images/".$dir)){
            mkdir("../media/user/images/".$dir); 
        }
        foreach ($_FILES['image'] as $key => $value){        
            $og_name = $_FILES['image']['name'][0];
            $type = $_FILES['image']['type'][0];
            $filename = explode("/", $type);
            $new_name = md5(rand()) . '.'.$filename[1];  
            $new_name = "{$filename[0]}-".substr($new_name,-11);
            $new_name = str_replace(" ","-",$new_name);
            $sourcePath = $_FILES['image']['tmp_name'][0];
            $targetPath = "../media/user/images/".$dir."/".$new_name;

            if(move_uploaded_file($sourcePath, $targetPath)){
                $data["url"] = $targetPath;
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
    }
}