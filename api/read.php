<?php
if(isset($_GET['atr']) && isset($_GET['bkey']) && isset($_GET['user']) && isset($_GET['token'])){
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
$book->id = trim($_GET['bkey']);
$book->token = trim($_GET['token']);
$book->author = trim($_GET['atr']);
$book->userID = trim($_GET['user']);
$book->path = "../json/users/bookdata/";

$bookInfo = $book->getSingleBook();
$singleBook = [];
$data = [
"index" => "",
"purchase_id" => $book->id,
"author_id" => $book->author,
"pathname" => $book->pathname,
"book_info" => $book->book_info,
"book_chapter" => $book->book_chapter,
"book_content" => $book->book_content,
"book_bg" => $book->book_bg,
"book_cover" => $book->book_cover,
"d_sound" => $book->d_sound,
"user_sound" => $book->user_sound,
"filename" => $book->filename
];

print_r(json_encode($data));

}else{
    die("No Data Found!");
}