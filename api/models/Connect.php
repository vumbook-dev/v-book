<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include('Net/SSH2.php');
include('Crypt/RSA.php');
require_once "./models/User.php";
class Connect extends User{

	//Protected
	private $_ssh;
	private $_pkey;

	//Private Action
	private $_terminalCommand;

	//PUBLIC
	public $pwd;
	public $log;
	public $user;
	public $tk;
	public $uFolder;
	private $_userData;
	public $bookdata;
	public $bookFileName;	
	public static $version = "alpha-1.0.0";
	private static $_versionfilepath = "/var/www/g-book/alpha-1.0.0";
	private $_mediaFolder;
	public $newDirectory;
	public $newFiles;

	// Constructor with DB
    public function __construct($db,$id,$tk) {
		$this->_conn = $db;
		$this->userID = $id;
		$this->token = $tk;
		$this->_userData = USER::verifyToken(true);
		$this->uFolder = $this->_userData['userpath'];
	}

	private function performExecCommand(){
		$this->_ssh = new Net_SSH2('192.168.1.138');
		$this->_pkey = new Crypt_RSA();
		$this->_pkey->loadKey(file_get_contents('/var/www/g-book/alpha-1.0.0/api/config/gbook.pem'));
		if (!$this->_ssh->login('vmbeditor', $this->_pkey)) {
			exit('Login Failed');
		}		
		$this->_ssh->exec($this->_terminalCommand);
		$this->pwd = $this->_ssh->exec('pwd');
		$this->log = $this->_ssh->exec('ls -la');
	}

	private function updateBookDirectoryPermission(){
		$this->_terminalCommand = "find ".$this->_versionfilepath."/json/users/bookdata/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->performExecCommand();
	}

	private function updateBookFilePermission(){
		$this->_terminalCommand = "find ".$this->_versionfilepath."/json/users/bookdata/".$this->uFolder."/. -type f -exec chmod 0666 {} \;";
		$this->performExecCommand();
	}

	private function updateMediaDirectoryPermission(){
		$this->mediaFolder = "".$this->_versionfilepath."/media/";
		$this->_terminalCommand = "find ".$this->mediaFolder."book-background/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->_terminalCommand .= " find ".$this->mediaFolder."page-background/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->_terminalCommand .= " find ".$this->mediaFolder."bookcover/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->_terminalCommand .= " find ".$this->mediaFolder."images/users/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->_terminalCommand .= " find ".$this->mediaFolder."sounds/users/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->performExecCommand();
	}

	public function createNewUserDirectory(){
		$this->_terminalCommand = "mkdir ". $this->newDirectory;
		$this->performExecCommand();
		$this->updateBookDirectoryPermission();
		$this->updateMediaDirectoryPermission();
	}

	public function createNewUserFiles(){
		$this->_terminalCommand = "touch ". $this->newFiles;
		$this->performExecCommand();
		$this->updateBookFilePermission();
	}

	public function updateNewBookFilePermission(){
		$this->_terminalCommand = "find ".$this->_versionfilepath."/json/users/bookdata/".$this->uFolder."/ -iname ".$this->bookFileName."* -type f -exec chmod 0666 {} \;";
		$this->performExecCommand();
	}

	public function testConnection(){
		$this->_terminalCommand = "ip a";
		$this->performExecCommand();
	}

}