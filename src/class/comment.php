<?php
class Comment{
	public $id_user;
	public $username;
	public $date;
	public $html;
	
	public function __construct($id_user, $username, $date, $html){
    	$this->id_user = $id_user;
    	$this->username = $username;
    	$this->date = $date;
    	$this->html = $html;
    }
}
?>