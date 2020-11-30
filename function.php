<?php
//DEFINE APP ROOT LINK
define('URLROOT','http://app.vumbook.test/',true);
define('VUMBOOK','http://vumbook.test/',true);
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
    $url = $_SERVER['REQUEST_URI'];
    $path = get_string_between($url,"/","/");
    //$template = get_string_between($url,"/","=");
    $book = substr($url, strpos($url, "=") + 1);
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
            $html .= "loadPage('create',vbloader);";          
            //return $template;   
        }        
    }else{
        $html .= 'usernotLoggedIn';
    }    

    return $html;
}

function contentForm($key = "", $vbID,$book,$bookIndex = ""){
    $html = '
    <div class="col-sm-12 tc-wrap d-none">        
        <div class="form-group">
        <form method="post" class="vb-new-section">
        <span class="vb-chapter'.$key.'">
            <label for="Section">Add New Section</label>
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

function setCurrentUser(){
    setcookie("userdata[id]","1");
    setcookie("userdata[name]","john");
    // $userID = $_COOKIE['userdata']['id'];
    // $userName = $_COOKIE['userdata']['name'];
    // echo $userID. " " .$userName;
}

//setCurrentUser();
