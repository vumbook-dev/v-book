<?php

function redirectToPages($path = ""){
    session_start();
    if(isset($_SESSION['page'])){
        $path = $_SESSION['page'];
        $state = $_SESSION['state'];        
        if(isset($_SESSION['book'])){
            $book = $_SESSION['book'];
            echo "history.pushState($state, `V-Book > $path`, `./$path/book={$book}`);";
            //echo "history.replaceState($state, `V-Book > $path`, `./$path/book={$book}`);";
            echo "sendToPage('$path',vbloader,$book);";
        }else{
            echo "history.pushState($state, `V-Book > $path`, `./$path/`);";
            //echo "history.replaceState($state, `V-Book > $path`, `./$path/`);";
            echo "loadPage('$path',vbloader);";
        }        
        echo "$('title').text(`V-Book > $path`);";
    }

    session_destroy();
}

function contentForm($key = "", $vbID,$book){
    $html = '
    <div class="col-sm-4 tc-wrap">        
        <div class="form-group">
        <span class="vb-chapter'.$key.'">
            <label for="Content">New Content</label>
            <input name="name'.$key.'" class="content-name form-control" type="text">
            <input type="hidden" data-title="'.$book.'" value="'.$vbID.'">
        </span>
        <button class="btn btn-primary px-3 float-right vb-new-content" style="margin-top:-38px;" data-key="'.$key.'" data-chapter="'.$key.'">Add</button>
        </div>
    </div>';

    return $html;
}