<?php
if(isset($_FILES['audio']) && $_FILES['audio']['name'] != ''){

    $media = file_get_contents("../json/users/user-sound.json");
    $media = json_decode($media);
    $og_count = count($media);
    $new_count = null;


    foreach ($_FILES['audio']['name'] as $key => $value){
        $og_name = $_FILES['audio']['name'][$key];
        $file_name = explode(".", $_FILES['audio']['name'][$key]);
        $new_name = md5(rand()) . '.' . $file_name[1];  
        $new_name = "{$og_name}-".substr($new_name,-11);
        $new_name = str_replace(" ","-",$new_name);
        $sourcePath = $_FILES['audio']['tmp_name'][$key];  
        $targetPath = "../media/sounds/user/".$new_name;  

        if(move_uploaded_file($sourcePath, $targetPath)){
            $new_count = $og_count + $key;
            $fileData = array("id" => "m{$new_count}", "alias" => $file_name[0], "filename" => $new_name);
            $media[] = $fileData;            
        }
    }

    if(is_numeric($new_count)){
        $json = json_encode($media);
        file_put_contents("../json/users/user-sound.json",$json);
        echo "Audio Successfully Uploaded";
    }

}elseif(isset($_POST['action']) && isset($_POST['file']) && isset($_POST['key'])){
    $file = $_POST['file'];
    $key = $_POST['key'];
    $list = file_get_contents("../json/book-content/{$file}.json");
    $content = json_decode($list);

    $mysounds = file_get_contents("../json/users/user-sound.json");
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
}