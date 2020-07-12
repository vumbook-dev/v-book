<?php

function redirectToPages($path = ""){
    session_start();
    if(isset($_SESSION['page'])){
        $path = $_SESSION['page'];
        $state = $_SESSION['state'];        
        if((isset($_SESSION['book']) && $state === 3) || (isset($_SESSION['book']) && $state === 4)){
            $book = $_SESSION['book'];
            echo "history.pushState($state, `V-Book > $path`, `./$path/book={$book}`);";
            //echo "history.replaceState($state, `V-Book > $path`, `./$path/book={$book}`);";
            if($path == "download"){
                echo "sendToPage('$path',vbloader,$book,'download');";
            }else{
                echo "sendToPage('$path',vbloader,$book);";
            }
        }else{
            echo "history.pushState($state, `V-Book > $path`, `./$path/`);";
            //echo "history.replaceState($state, `V-Book > $path`, `./$path/`);";            
            echo "loadPage('$path',vbloader);";             
        }        
        echo "$('title').text(`V-Book > $path`);";
    }

    session_destroy();
}

function contentForm($key = "", $vbID,$book,$bookIndex = ""){
    $html = '
    <div class="col-sm-4 tc-wrap">        
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