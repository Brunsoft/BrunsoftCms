<?php
class Article{
    public $name_article;
    public $category;
    public $html;
    public $date_creation;
    public $date_last_edit;
    public $user_author;
    public $user_last_edit;
    
    public function __construct($name_article, $mysqli){
    	$this->init($name_article, $mysqli);
    }
    
    public function init($name_article, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT category, html, date_creation, date_last_edit, user_author, user_last_edit FROM article WHERE name_article = ?")) {
			$stmt->bind_param('s', $name_article); 
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($category, $html, $date_creation, $date_last_edit, $user_author, $user_last_edit); 
			if ($stmt->affected_rows > 0) {
				while ($stmt->fetch()) {
					$this->name_article = $name_article;
					$this->category = $category;
					$this->html = $html;
					$this->date_creation = $date_creation;
					$this->date_last_edit = $date_last_edit;
					$this->user_author = $user_author;
					$this->user_last_edit = $user_last_edit;
				}
			}
	   	}
	}

}
