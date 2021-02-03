<?php
//DEFINE APP ROOT LINK
require_once "config.php";

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function redirectToPages($path = ""){
    $html = "";
    $url = rtrim($_SERVER['REQUEST_URI'],"/");
    $failsafe = (isset($_GET['failsafe_mode'])) ? $_GET['failsafe_mode'] : "";
    $path = explode('/', $url);
    $path = (empty($path[1])) ? "" : $path[1];
    //$book = substr($url, strpos($url, "=") + 1);
    //$template = get_string_between($url,"/","=");
    $book = (empty($failsafe)) ? substr($url, strpos($url, "=") + 1) : str_replace("?failsafe_mode=".$failsafe,"",substr($url, strpos($url, "=") + 1));
    switch($path){
        case "editor"; $state = 1; break;
        case "create"; $state = 2; break;
        case "table-of-contents"; $state = 3; break;
        case "read"; $state = 4; break;
        default: $state = 0; break;
    }
    if(!empty($_COOKIE['userdata'])){
        if((!empty($book) && $state === 3) || (!empty($book) && $state === 4)){
            //$html .= "history.pushState($state, `V-Book > $path`, `./$path/book={$book}`);";
            //echo "history.replaceState($state, `V-Book > $path`, `./$path/book={$book}`);";
            if($path == "download"){
                $html .= "sendToPage('$path',vbloader,$book,'download');";
            }else{
                $html .= "sendToPage('$path',vbloader,$book);";
            }            
        }elseif(!empty($path)){
            //"history.pushState($state, `V-Book > $path`, `./$path/`);";
            //echo "history.replaceState($state, `V-Book > $path`, `./$path/`);";        
            // if(createUserFolders()){
            //     createUserFiles();
            //     $html .= "loadPage('create',vbloader);";
            // }else{
                $html .= "loadPage('create',vbloader);";
            //} 
        }
    }else{
        $html .= 'usernotLoggedIn';
    }    

    return $html;
}

function contentForm($key = "", $vbID = "",$book = "",$bookIndex = ""){
    $html = '
    <div class="col-sm-12 tc-wrap d-none">        
        <div class="form-group">
        <form method="post" class="vb-new-section">
        <span class="vb-chapter'.$key.'">
            <label for="Section">Add New Chapter</label>
            <input name="name'.$key.'" class="content-name form-control" type="text">
            <input type="hidden" data-bookIndex="'.$bookIndex.'" data-title="'.$book.'" value="'.$vbID.'">
        </span>
        <button class="btn btn-primary px-3 float-right vb-new-content" style="margin-top:-38px;" data-key="'.$key.'" data-chapter="'.$key.'">Submit</button>
        </form>
        </div>
    </div>';

    return $html;
}

function revertTextToEditor($post){
    $new = str_replace(", </span><span>",",",$post);
    $new = str_replace(". </span><span>",".",$new);
    $new = str_replace(": </span><span>",":",$new);
    $new = str_replace("; </span><span>",";",$new);
    $new = str_replace("<span>","<p>",$new);
    $new = str_replace("</span>","</p>",$new);
    return $new;
}

//GET FILE PERMISSION
function getPermission(){
    changeFilePermission("json/users/bookdata/".DATAPATH."/book-content/Thyroid-2a9364.json");
    $sound = substr(sprintf('%o', fileperms("media/sounds/users/".DATAPATH)), -4);
    $images = substr(sprintf('%o', fileperms("media/images/users/".DATAPATH)), -4);
    $cover = substr(sprintf('%o', fileperms("media/bookcover/users/".DATAPATH)), -4);
    $background = substr(sprintf('%o', fileperms("media/book-background/users/".DATAPATH)), -4);
    $page = substr(sprintf('%o', fileperms("media/page-background/users/".DATAPATH)), -4);
    $bookData = substr(sprintf('%o', fileperms("json/users/bookdata/".DATAPATH."/book-content/Thyroid-2a9364.json")), -4);
    $json = [
        "sound" => $sound,
        "images" => $images,
        "cover" => $cover,
        "background" => $background,
        "page" => $page,
        "file" => $bookData,
    ];
    echo json_encode($json);
}

//CHANGE FILE PERMISSION
function changeFilePermission($file){
    $mode = 644;
    chmod($file, octdec($mode));
}

//CREATE USER FOLDERS
function createUserFolders(){
    if(isset($_COOKIE['userdata'])){
        $accountType = $_COOKIE['userdata']['account_type'];
        if($accountType == 'author'){
            $UID = $_COOKIE['userdata']['id'];
            $UName = $_COOKIE['userdata']['name'];
            $UFolder = DATAPATH;
            $booklist = "json/users/bookdata/{$UFolder}/books-list-title.json";            

            if(file_exists($booklist)){
                return false;   
            }else{
                $allUserFolders = array(
                    "book_background" => "media/book-background/{$UFolder}",
                    "page_cover" => "media/page-background/{$UFolder}",
                    "book_cover" => "media/bookcover/{$UFolder}",
                    "book_images" => "media/images/users/{$UFolder}",
                    "media_sounds" => "media/sounds/users/{$UFolder}",
                    "user_json" => "json/users/bookdata/{$UFolder}",
                    "media_json" => "json/users/bookdata/{$UFolder}/media",
                    "bookchapter_json" => "json/users/bookdata/{$UFolder}/book-chapter",
                    "bookcontent_json" => "json/users/bookdata/{$UFolder}/book-content",
                );
                //Create User Folders
                foreach($allUserFolders as $value){
                    if(!is_dir($value)){
                        mkdir($value);            
                    }
                }  
                return true;
            }
        }
    }
}

//CREATE USER INITIAL NECESSARY FILES
function createUserFiles(){
    $accountType = $_COOKIE['userdata']['account_type'];
    if($accountType == 'author'){
        $UID = $_COOKIE['userdata']['id'];
        $UName = $_COOKIE['userdata']['name'];
        $UFolder = DATAPATH;
        $path = "json/users/bookdata/{$UFolder}";
        $booklist = "{$path}/books-list-title.json";
        $files = array(
            "booklist" => "{$path}/books-list-title.json",
            "archive" => "{$path}/archive-book-title.json",
            "background" => "{$path}/media/user-background.json",
            "cover" => "{$path}/media/user-bookcover.json",
            "sounds" => "{$path}/media/user-sound.json"
        );

        if(!file_exists($booklist)){
            //Create Initial Book List Files
            foreach($files as $key => $value){
                file_put_contents($value,"[]");
            }    
            return true;    
        }
    }
}


//TEST FAIL SAFE FUNCTION
function failsafe_test(){
    $msg = "";            
    if($_GET['failsafe_mode'] === 'enabled' || $_GET['failsafe_mode'] === 'disabled'){
        require_once "./model/debug_helper.php";
        $debug = ($_GET['failsafe_mode'] == 'enabled') ? true : false;
        $json = "Some bunch of dummy text Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce eu orci in magna pulvinar volutpat a et nunc. Sed mattis vitae erat ac pellentesque.";
        $helper = new Helper($debug);
        $helper->failSafe($json,197,"content.php");
        if($debug){
            $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG, "data" => $json);
        }else{
            $msg = array("mode" => $helper->mode, "errorType" => $helper->errorType, "errorMSG" => $helper->errorMSG);
        }
        
        return json_encode($msg);
    }else{
        return $msg;
    }
}