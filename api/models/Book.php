<?php 
  class Book {
    // DB stuff
    private $_conn;
    private $_purchasedTable = 'purchased';
    private $_userTable = 'users';
    private $_bookIndex;
    private $_singleBook;
    // Set User if verified
    private $_userVerified;

    // Required Data To Set Request
    public $id;
    public $userID;
    public $author;
    public $account;
    public $token;
    public $created_at;
    public $attempt;

    // Book Content Holder
    public $book_content;
    public $book_info;
    public $book_chapter;
    public $book_bg;
    public $book_cover;
    public $d_sound;
    public $user_sound;
    public $filename;
    private $urlroot = "../";
    public $path;
    public $pathname;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Verify User Token
    private function verifyToken(){
      $query = 'SELECT * FROM ' . $this->_userTable . ' u WHERE u.token = :token AND u.id = :userID';
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':token', $this->token);
      $stmt->bindParam(':userID', $this->userID);
      // Execute query
      $stmt->execute();
      $count = $stmt->rowCount();
      if($count !== 1) die("Token Expired!"); 
    }

    // Get Single Book
    public function getSingleBook(){
        $this->verifyToken();
        $query = 'SELECT p.book_id as book, p.author_id as author, p.id as purchasedID, CONCAT(u.username,u.id) as pathname FROM ';
        $query .= $this->_purchasedTable . ' p INNER JOIN ' . $this->_userTable . ' u ON u.id = p.author_id WHERE p.book_id = :bookID AND p.user_id = :userID';
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bookID', $this->id);
        $stmt->bindParam(':userID', $this->userID);
        // Execute query
        $stmt->execute();
        $count = $stmt->rowCount();
        if($count > 0){
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $this->pathname = $row['pathname'];
          $this->attempt = "success";

        //Fetch Book Data
        if($this->author ===  $row['author'] && $this->id === $row['book']){
            //Load Necessary Files
            $path = "{$this->path}{$this->pathname}";
            $booklist = json_decode(file_get_contents($path."/books-list-title.json"),true);
            $this->book_bg = json_decode(file_get_contents($path."/media/user-background.json"),true);
            $this->book_cover = json_decode(file_get_contents($path."/media/user-bookcover.json"),true);
            $this->d_sound = json_decode(file_get_contents("{$this->urlroot}json/media/default-sounds.json"),true);

            //Get Book Info
            foreach($booklist as $k => $value){
                if($value['id'] === $row['book']){
                  $this->_singleBook = $value;
                  $this->_bookIndex = $k;
                }
            }
        }

        if(count($this->_singleBook) > 0){
            $file = $this->_singleBook['storage'];
            $this->filename = $file;
            $this->book_chapter = json_decode(file_get_contents($path."/book-chapter/{$file}.json"),true);
            $this->book_content = json_decode(file_get_contents($path."/book-content/{$file}.json"),true);
            $this->user_sound = json_decode(file_get_contents($path."/media/user-sound.json"),true);        
            $this->book_info = $this->_singleBook;
        }

        }else{
          $this->attempt = "nodata";
        }        
    }    

    // Verify Book Author
    public function authorsBook(){
        $query = 'SELECT CONCAT(u.username,u.id) as pathname FROM ' . $this->_userTable . ' u WHERE u.id = :userID AND u.account_type = :accountTYPE AND u.token = :token LIMIT 0, 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $this->userID);
        $stmt->bindParam(':accountTYPE', $this->account);
        $stmt->bindParam(':token', $this->token);
        $stmt->execute();
        $num = $stmt->rowCount();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Validate If Access is valid;
        return ($num > 0) ? $row['pathname'] : false;
    }

  }