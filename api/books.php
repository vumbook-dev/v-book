<?php
if(isset($_GET['atr']) && isset($_GET['token'])){
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once './config/Database.php';
    include_once './models/Book.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog post object
    $book = new Book($db);
    $book->userID = trim($_GET['atr']);
    $book->token = trim($_GET['token']);
    $book->group_id = (isset($_GET['group'])) ? trim($_GET['group']) : "";
    $book->account = "author";
    $result = (!isset($_GET['group'])) ? $book->authorsBook() : $book->teamFetchBook();
    if($result){
        $booklist = file_get_contents("../json/users/bookdata/{$result}/books-list-title.json");
        print_r($booklist);
    }else{
        die("Denied!");
    }
}else{
    die("Users Book!");
}
