<?php

function redirectToPages($path = ""){
    session_start();
    if(isset($_SESSION['page'])){
        $path = $_SESSION['page'];
        $state = $_SESSION['state'];
        echo "$('a[data-nav=$path]').click();";
        echo "history.pushState($state, `V-Book > $path`, `./$path`);";
    }else{
        echo "console.log('Nothing')";
    }

    session_destroy();
}