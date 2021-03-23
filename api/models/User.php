<?php 
class User {
    // Required Data To Set Request    
    public $userID;
    public $author;
    public $account;
    public $group_id;
    public $token;
    public $created_at;
    public $attempt;

    //DB Privates
    protected $_purchasedTable = 'book_purchased';
    protected $_userTable = 'users';
    protected $_groupTable = 'group_teams';
    public $_conn;

    // Verify User Token
    protected function verifyToken($return = false){
        $query = 'SELECT *, CONCAT(username,id) as userpath FROM ' . $this->_userTable . ' u WHERE u.token = :token AND u.id = :userID';
        // Prepare statement
        $stmt = $this->_conn->prepare($query);
        $stmt->bindParam(':token', $this->token);
        $stmt->bindParam(':userID', $this->userID);
        // Execute query
        $stmt->execute();
        $count = $stmt->rowCount();
        if($count !== 1) die("Token Expired!"); 
        return ($return) ? $stmt->fetch(PDO::FETCH_ASSOC) : true;
    }
}