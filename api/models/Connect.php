<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include('Net/SSH2.php');
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
	private $uFolder;
	public $bookdata;
	public $bookFileName;	
	public static $version = "alpha-1.0.0";
	private $mediaFolder;
	public $newDirectory;
	public $newFiles;

	// Constructor with DB
    public function __construct($db) {
		$this->_conn = $db;
		$this->uFolder = USER::verifyToken(true);
	}

	private function updatePermission(){
		$this->_ssh = new Net_SSH2('192.168.1.138');
		$this->_pkey = "rbKhpjWR6T9xKuLjwj8Ptwn5a8Pe332YRZHFekEGF2N3gdGz";
		if (!$this->_ssh->login('gbeditor', $this->_pkey)) {
		    exit('Login Failed');
		}		
		$this->_ssh->exec($this->_terminalCommand);
		$this->pwd = $this->_ssh->exec('pwd');
		$this->log = $this->_ssh->exec('ls -la');
	}

	private function updateBookDirectoryPermission(){
		$this->_terminalCommand = "find /var/www/g-book/".$this->version."/json/users/bookdata/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->updatePermission();
	}

	private function updateBookFilePermission(){
		$this->_terminalCommand = "find /var/www/g-book/".$this->version."/json/users/bookdata/".$this->uFolder."/. -type f -exec chmod 0666 {} \;";
		$this->updatePermission();
	}

	private function updateMediaDirectoryPermission(){
		$this->mediaFolder = "/var/www/g-book/".$this->version."/media/";
		$this->_terminalCommand = "find ".$this->mediaFolder."book-background/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->_terminalCommand .= " find ".$this->mediaFolder."page-background/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->_terminalCommand .= " find ".$this->mediaFolder."bookcover/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->_terminalCommand .= " find ".$this->mediaFolder."images/users/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->_terminalCommand .= " find ".$this->mediaFolder."sounds/users/".$this->uFolder."/. -type d -exec chmod 0777 {} \;";
		$this->updatePermission();
	}

	public function createNewUserDirectory(){
		$this->_terminalCommand = "mkdir ". $this->newDirectory;
		$this->updatePermission();
		$this->updateBookDirectoryPermission();
		$this->updateMediaDirectoryPermission();
	}

	public function createNewUserFiles(){
		$this->_terminalCommand = "touch ". $this->newFiles;
		$this->updatePermission();
		$this->updateBookFilePermission();
	}

	public function updateNewBookFilePermission(){
		$this->_terminalCommand = "find /var/www/g-book/".$this->version."/json/users/bookdata/".$this->uFolder."/ -iname ".$this->bookFileName."* -type f -exec chmod 0666 {} \;";
		$this->updatePermission();
	}

}