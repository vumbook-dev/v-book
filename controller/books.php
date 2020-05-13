<?php
if(isset($_POST['action'])){

    $action = $_POST['action'];
    //$allbooks = file_get_contents("../json/books-list-title.json");
    
    if($action == "add"){
        //Creat Book Title and Database
        if(isset($_POST['title'])){
            $sub = (isset($_POST['subTitle'])) ? $_POST['subTitle'] : "";
            $title = $_POST['title'];
            $hash = "$title".rand(0,1000);
            $id = md5($hash);
            $newBook = array("id" => $id, "title" => $title, "subtitle" => $sub, "status" => "unpublished", "chapter" => array());
            $oldData = file_get_contents("../json/books-list-title.json");
            $arrayData = json_decode($oldData,true);
            $arrayData[] = $newBook;
            $json = json_encode($arrayData);
            file_put_contents("../json/books-list-title.json",$json);
            echo $title;
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
            $archive = array("id" => $active[$k]['id'], "title" => $active[$k]['title'], "subtitle" => $active[$k]['subtitle'], "status" => $active[$k]['status'], "unpublished", "chapter" => $active[$k]['chapter']);
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
        }
    }

}
