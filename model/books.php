<?php
if(isset($_POST['action'])){

    $action = $_POST['action'];
    //$allbooks = file_get_contents("../json/books-list-title.json");
    
    if($action == "add"){
        //Creat Book Title and Database
        if(isset($_POST['title'])){
            $sub = (isset($_POST['subTitle'])) ? $_POST['subTitle'] : "";
            $title = $_POST['title'];
            $hshtitle = str_replace(" ","-",$title);
            $hash = "$title".rand(0,1000);
            $id = md5($hash);
            $storage = "{$hshtitle}-".substr($id,-6);
            $newBook = array("id" => $id, "title" => $title, "subtitle" => $sub, "storage" => $storage, "status" => "unpublished", "cover" => null, "speed" => "1000", "bgType" => "color", "bgValue" => "#fff", "dsound" => "0", "dAlign" => "center", "chapter" => array());
            $oldData = file_get_contents("../json/books-list-title.json");
            $arrayData = json_decode($oldData,true);
            $arrayData[] = $newBook;
            $countBook = count($arrayData);
            $json = json_encode($arrayData);
            file_put_contents("../json/books-list-title.json",$json);
            file_put_contents("../json/book-content/{$storage}.json","[]");
            echo $countBook;
            //$_POST = array();
        }
    }

    elseif($action == "delete"){
        //Delete Book Data Inside json
        if(isset($_POST['key'])){
            $k = $_POST['key'];
            $allData = file_get_contents("../json/books-list-title.json");
            $archivedData = file_get_contents("../json/archive-book-title.json");
            $active = json_decode($allData,true);
            $inactive = json_decode($archivedData,true);
            $archive = array("id" => $active[$k]['id'], "title" => $active[$k]['title'], "subtitle" => $active[$k]['subtitle'], "storage" => $active[$k]['storage'], "status" => $active[$k]['status'], "cover" => $active[$k]['cover'], "speed" => $active[$k]['speed'], "dsound" => $active[$k]['dsound'], "dAlign" => $active[$k]['dAlign'], "chapter" => $active[$k]['chapter']);
            unset($active[$k]);
            $active = array_values($active);

            //NEW SET OF BOOKS
            $newActive = json_encode($active);
            file_put_contents("../json/books-list-title.json",$newActive);

            $inactive[] = $archive;
            $newInActive = json_encode($inactive);
            file_put_contents("../json/archive-book-title.json",$newInActive);
            echo $archive['title'];
            //print_r($data);
            //$_POST = array();
        }
    }

    elseif($action == 'update'){
        if(isset($_POST['key']) && isset($_POST['bgType']) && isset($_POST['bgValue'])){
            $k = $_POST['key'];
            $allData = file_get_contents("../json/books-list-title.json");
            $book = json_decode($allData,true);
            $book[$k]["bgType"] = $_POST['bgType'];
            $book[$k]["bgValue"] = $_POST['bgValue'];

            $newUpdate = json_encode($book);
            file_put_contents("../json/books-list-title.json",$newUpdate);
            //print_r($book);
            //echo $_POST['bgValue']." ".$k." ".$_POST['bgType']." ".$book[$k]["bgValue"];
        }
    }

}elseif(isset($_FILES['book-cover']) && $_FILES['book-cover']['name'] != ''){
    $media = file_get_contents("../json/users/user-bookcover.json");
    $media = json_decode($media);
    $og_count = count($media);
    $book = $_POST['book'];
    $oldData = file_get_contents("../json/books-list-title.json");
    $arrayData = json_decode($oldData);    
    //$new_count = null;

    //foreach ($_FILES['book-cover']['name'] as $key => $value){
        $og_name = $_FILES['book-cover']['name'][0];
        $file_name = explode(".", $_FILES['book-cover']['name'][0]);
        $new_name = md5(rand()) . '.' . $file_name[1];  
        $new_name = "{$og_name}-".substr($new_name,-11);
        $new_name = str_replace(" ","-",$new_name);
        $sourcePath = $_FILES['book-cover']['tmp_name'][0];  
        $targetPath = "../media/bookcover/user/".$new_name;  

        if(move_uploaded_file($sourcePath, $targetPath)){
            $new_count = $og_count;
            $fileData = array("id" => $new_count, "alias" => $file_name[0], "filename" => $new_name, "book" => $book);
            $media[] = $fileData;
            $arrayData[$book]->cover = $new_count;                        
        }
    //}

    if(is_numeric($new_count)){
        $json = json_encode($media);        
        $newcover = json_encode($arrayData);        
        file_put_contents("../json/books-list-title.json",$newcover);
        file_put_contents("../json/users/user-bookcover.json",$json);
        //print_r($arrayData);
        echo $targetPath;
    }
}
