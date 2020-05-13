<?php

$path = $_SERVER['REQUEST_URI'];
$path = ltrim($path,"/");
switch($path){
    case "editor"; $state = 1; break;
    case "create-books"; $state = 2; break;
    case "book-chapter"; $state = 3; break;
    default: $state = 0; break;
}

session_start();

if($state !== 0){
    $_SESSION['page'] = $path;
    $_SESSION['state'] = $state;
}

header("Location: /");