<?php
require('src/class/page.php');
require('src/pagine/function_page.php');

class Model{
    public $page;
    public $mysqli;

    public function __construct($mysqli){
    	$this->mysqli = $mysqli;
    }
    
    public function init($permaname){
    	$page_id = getIdPagePermaname($permaname, $this->mysqli);
    	$this->page = new Page($page_id, $this->mysqli);    	
	}
	
	public function getPageSel($permaname){
    	$page_id = getIdPagePermaname($permaname, $this->mysqli);
    	return new Page($page_id, $this->mysqli);    	
	}
	
	public function searchPage($permaname, $main_permaname){
		return searchPagePermaname($permaname, $main_permaname, $this->mysqli);
	}

}
?>