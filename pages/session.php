<?php
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$url = $_SERVER['REQUEST_URI'];
$path = get_string_between($url,"/","/");
$book = substr($url, strpos($url, "?") + 1);
switch($path){
    case "editor"; $state = 1; break;
    case "create-books"; $state = 2; break;
    case "book-chapter"; $state = 3; break;
    default: $state = 0; break;
}

session_start();

if($state !== 0 && $state !== 3){
    $_SESSION['page'] = $path;
    $_SESSION['state'] = $state;
}elseif(is_numeric($book)){
    $_SESSION['page'] = $path;
    $_SESSION['state'] = $state;
    $_SESSION['book'] = $book;
}
// echo $path;
// echo $book;
header("Location: /");