<?php

function redirectToPages($path = ""){
    session_start();
    if(isset($_SESSION['page'])){
        $path = $_SESSION['page'];
        $state = $_SESSION['state'];
        echo "loadPage('$path',vbloader);";
        echo "history.pushState($state, `V-Book > $path`, `./$path`);";
        echo "$('title').text(`V-Book > $path`);";
    }

    session_destroy();
}