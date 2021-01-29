<?php 
if(isset($_GET['atr']) && isset($_GET['token']) && isset($_GET['action'])){

//SHOW ERROR
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
require_once './config/Database.php';
require_once "./models/Connect.php";

//Instantiate DB & connect
$database = new Database();
$db = $database->connect();

$connect = new Connect($db,$_GET['atr'],$_GET['token']);
$UFolder = $connect->uFolder;
$version = $connect::$version;

$path = "/var/www/g-book/".$version;

$allUserFolders = array(
    "book_background" => "{$path}/media/book-background/{$UFolder}",
    "page_cover" => "{$path}/media/page-background/{$UFolder}",
    "book_cover" => "{$path}/media/bookcover/{$UFolder}",
    "book_images" => "{$path}/media/images/users/{$UFolder}",
    "media_sounds" => "{$path}/media/sounds/users/{$UFolder}",
    "user_json" => "{$path}/json/users/bookdata/{$UFolder}",
    "media_json" => "{$path}/json/users/bookdata/{$UFolder}/media",
    "bookchapter_json" => "{$path}/json/users/bookdata/{$UFolder}/book-chapter",
    "bookcontent_json" => "{$path}/json/users/bookdata/{$UFolder}/book-content",
);

$extendPath = $path."/json/users/bookdata/".$UFolder."/";
$files = array(
    "booklist" => "{$extendPath}/books-list-title.json",
    "archive" => "{$extendPath}/archive-book-title.json",
    "background" => "{$extendPath}/media/user-background.json",
    "cover" => "{$extendPath}/media/user-bookcover.json",
    "sounds" => "{$extendPath}/media/user-sound.json"
);

//Perform Action
switch($_GET['action']) :
case "new_user";
//Create New Directory
$directory = ""; foreach($allUserFolders as $val){ $directory .= $val." "; } $connect->newDirectory = $directory; $connect->createNewUserDirectory();
//Create New User Files
$newFiles = ""; foreach($files as $file){ if(!file_exists($file)) $newFiles .= $file." "; } $connect->newFiles = $newFiles; $connect->createNewUserFiles();
break;
//Test Connection
case "test_connection";
$connect->testConnection();
die("PWD: ".$connect->pwd."<br> Log: ".$connect->log);
break;
endswitch;

}else{
    die("Invalid Request");
}