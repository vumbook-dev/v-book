<?php
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
    $UFolder = "{$UName}{$UID}";
    if(isset($_POST['action'])){
        $action = $_POST['action'];
        //$allbooks = file_get_contents("../json/books-list-title.json");
        
        if($action == "add"){
            //Creat Book Title and Database
            if(isset($_POST['title'])){
                $sub = (isset($_POST['subTitle'])) ? $_POST['subTitle'] : "";
                $title = $_POST['title'];
                $template = $_POST['template'];
                $path = "../json/users/bookdata/{$UFolder}/";
                $contentArray = ($template == 'book') ? [] : "";
                $hshtitle = str_replace(" ","-",$title);
                $hash = "$title".rand(0,1000);
                $id = md5($hash);
                $storage = "{$hshtitle}-".substr($id,-6);
                $chapterArray = array();
                $bookInformation = array("name" => "Book Info","bgType" => "color","background" => "#fff");
                $bookInformation = json_encode($bookInformation);
                $chapterArray[] = $bookInformation;
                $newBook = array("id" => $id, "title" => $title, "subtitle" => $sub, "storage" => $storage, "status" => "unpublished", "cover" => null, "speed" => "1000", "dsound" => "0", "chapter" => $chapterArray,"template" => $template,"bookInfoSkip" => 2);
                $oldData = file_get_contents("../json/users/bookdata/{$UFolder}/books-list-title.json");
                $arrayData = json_decode($oldData,true);
                $arrayData[] = $newBook;
                $countBook = count($arrayData);
                $json = json_encode($arrayData);
                $contentlist = array();
                $CRP = array("id" => "00","chapter" => 0, "cpart" => "Copyright Page", "sound" => 0, "volume" => 0.5, "content" => $contentArray, "bgType" => "color", "background" => "#fff");
                $TBLC = array("id" => "01","chapter" => 0, "cpart" => "Table of Contents", "sound" => 0, "volume" => 0.5, "content" => $contentArray, "bgType" => "color", "background" => "#fff");
                $contentlist[] = $CRP;
                $contentlist[] = $TBLC;
                $ARR = array_column($contentlist, 'id');
                array_multisort($ARR, SORT_ASC, $contentlist);
                $contentSection = json_encode($contentlist);
                file_put_contents("{$path}books-list-title.json",$json);
                file_put_contents("{$path}book-content/{$storage}.json",$contentSection);
                file_put_contents("{$path}book-chapter/{$storage}.json","[{$bookInformation}]");
                echo $countBook;
                //$_POST = array();
            }
        }

        elseif($action == "delete"){
            //Delete Book Data Inside json
            if(isset($_POST['key'])){
                $k = $_POST['key'];
                $path = "../json/users/bookdata/{$UFolder}/";
                $allData = file_get_contents("{$path}books-list-title.json");
                $archivedData = file_get_contents("{$path}archive-book-title.json");
                $active = json_decode($allData,true);
                $inactive = json_decode($archivedData,true);
                $archive = array("id" => $active[$k]['id'], "title" => $active[$k]['title'], "subtitle" => $active[$k]['subtitle'], "storage" => $active[$k]['storage'], "status" => $active[$k]['status'], "cover" => $active[$k]['cover'], "speed" => $active[$k]['speed'], "dsound" => $active[$k]['dsound'], "chapter" => $active[$k]['chapter']);
                unset($active[$k]);
                $active = array_values($active);
                $filename = $archive['storage'];

                //NEW SET OF BOOKS
                $newActive = json_encode($active);
                file_put_contents("{$path}books-list-title.json",$newActive);

                //DELETE BOOK DATA
                unlink("{$path}book-chapter/{$filename}.json");
                unlink("{$path}book-content/{$filename}.json");

                // $inactive[] = $archive;
                // $newInActive = json_encode($inactive);
                // file_put_contents("{$path}archive-book-title.json",$newInActive);
                echo $archive['title'];
                //print_r($data);
                //$_POST = array();
            }
        }

        elseif($action == "update_title"){
            if(isset($_POST['title']) && isset($_POST['book'])){
                $fullTitle = $_POST['title'];
                $newTitle = explode("{",$fullTitle);
                if(!empty($newTitle[1])){
                    $title = $newTitle[0];
                    $subtitle = rtrim($newTitle[1],"}");
                }else{
                    $title = $newTitle[0];
                    $subtitle = "";
                }
                
                $bk = $_POST['book'];
                $path = "../json/users/bookdata/{$UFolder}/books-list-title.json";
                $booklist = file_get_contents($path);
                $booklist = json_decode($booklist,true);
                $booklist[$bk]['title'] = $title;
                $booklist[$bk]['subtitle'] = $subtitle;
                $newUpdate = json_encode($booklist);
                file_put_contents($path,$newUpdate);
                echo "Book Title Updated to \"{$title} {$subtitle}\"";
            }
        }elseif($action == "loadBC"){
            if(isset($_POST['file']) && isset($_POST['path']) && isset($_POST['section'])){
                $author = $_POST['path'];
                $file = $_POST['file'];
                $section = $_POST['section'];
                $path = "../json/users/bookdata/{$author}/book-content/{$file}.json";
                $booklist = file_get_contents($path);
                $booklist = json_decode($booklist);
                $book = $booklist[$section];
                $json = array("id" => $book->id, "content" => $book->content);
                echo json_encode($json);
            }
            
        }

        // elseif($action == 'update'){
        //     if(isset($_POST['key']) && isset($_POST['bgType']) && isset($_POST['bgValue']) && isset($_POST['file'])){
        //         $k = $_POST['key'];
        //         $file = $_POST['file'];
        //         $allData = file_get_contents("../json/book-content/{$file}.json");
        //         $section = json_decode($allData,true);
        //         $section[$k]["bgType"] = $_POST['bgType'];
        //         $section[$k]["bgValue"] = $_POST['background'];

        //         $newUpdate = json_encode($book);
        //         //file_put_contents("../json/book-content/{$file}.json",$newUpdate);
        //     }
        // }

    }elseif(isset($_FILES['book-cover']) && $_FILES['book-cover']['name'] != ''){
        $media = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-bookcover.json");
        $media = json_decode($media);
        $og_count = count($media);
        $book = $_POST['book'];
        $oldData = file_get_contents("../json/users/bookdata/{$UFolder}/books-list-title.json");
        $arrayData = json_decode($oldData);    
        //$new_count = null;

        //foreach ($_FILES['book-cover']['name'] as $key => $value){
            $og_name = $_FILES['book-cover']['name'][0];
            $file_name = explode(".", $_FILES['book-cover']['name'][0]);
            $new_name = md5(rand()) . '.' . $file_name[1];  
            $new_name = "{$og_name}-".substr($new_name,-11);
            $new_name = str_replace(" ","-",$new_name);
            $sourcePath = $_FILES['book-cover']['tmp_name'][0];  
            $targetPath = "../media/bookcover/{$UFolder}/".$new_name;  

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
            file_put_contents("../json/users/bookdata/{$UFolder}/books-list-title.json",$newcover);
            file_put_contents("../json/users/bookdata/{$UFolder}/media/user-bookcover.json",$json);
            $result = array("target" => $targetPath, "type" => "cover", "message" => "Cover");
            echo json_encode($result);
        }
    }elseif(isset($_FILES['book-background']) && $_FILES['book-background']['name'] != ''){
        $media = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-bookcover.json");
        $media = json_decode($media);
        $og_count = count($media);
        $book = $_POST['book'];
        $oldData = file_get_contents("../json/users/bookdata/{$UFolder}/books-list-title.json");
        $arrayData = json_decode($oldData);    

        $og_name = $_FILES['book-background']['name'][0];
        $file_name = explode(".", $_FILES['book-background']['name'][0]);
        $new_name = md5(rand()) . '.' . $file_name[1];  
        $new_name = "{$file_name[0]}-".substr($new_name,-11);
        $new_name = str_replace(" ","-",$new_name);
        $sourcePath = $_FILES['book-background']['tmp_name'][0];  
        $targetPath = "../media/book-background/{$UFolder}/".$new_name;  

        if(move_uploaded_file($sourcePath, $targetPath)){
            $new_count = $og_count;
            $fileData = array("id" => $new_count, "alias" => $file_name[0], "filename" => $new_name, "book" => $book);
            $media[] = $fileData;
            $arrayData[$book]->background = $new_count;                        
        }

        if(is_numeric($new_count)){
            $json = json_encode($media);        
            $newcover = json_encode($arrayData);        
            file_put_contents("../json/users/bookdata/{$UFolder}/books-list-title.json",$newcover);
            file_put_contents("../json/users/bookdata/{$UFolder}/media/user-background.json",$json);
            
            $result = array("target" => $targetPath, "type" => "background", "message" => "Background");
            echo json_encode($result);
        }
    }
}