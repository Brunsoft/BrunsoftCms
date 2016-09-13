<?php
class Database{
	private $host;
    private $user;
    private $password;
    private $database;
    public $mysqli;

    public function __construct(){
    	$this->host = 'localhost';
    	$this->user = 'root';
    	$this->password = '';
    	$this->database = 'db_mvc';
    	
        $this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);
    }

}
?>