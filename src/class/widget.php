<?php
require_once('submenu.php');

class Widget{
    public $name_widget;
    public $html;
    public $ord;
    public $pos;
    public $type;
    public $dim;
    public $class;
    public $date_creation;
    public $date_last_edit;
    public $user_author;
    public $user_last_edit;
    public $published;
    public $sub_menu;			// Eventuale array oggetti SubMenu in caso di widget type menu
    
    public function __construct($name_widget, $mysqli){
    	$this->sub_menu = array();
    	$this->init($name_widget, $mysqli);
    }
    
    public function init($name_widget, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT html, ord, pos, type, dim, class, date_creation, date_last_edit, user_author, user_last_edit, published FROM widget WHERE name_widget = ?")) {
			$stmt->bind_param('s', $name_widget); 
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($html, $ord, $pos, $type, $dim, $class, $date_creation, $date_last_edit, $user_author, $user_last_edit, $published); 
			if ($stmt->affected_rows > 0) {
				while ($stmt->fetch()) {
					$this->name_widget = $name_widget;
					$this->html = $html;
					$this->ord = $ord;
					$this->pos = $pos;
					$this->type = $type;
					$this->dim = $dim;
					$this->class = $class;
					$this->date_creation = $date_creation;
					$this->date_last_edit = $date_last_edit;
					$this->user_author = $user_author;
					$this->user_last_edit = $user_last_edit;
					$this->published = $published;
				}
			}
	   	}
	   	if(strcmp($type, "menu") == 0){
			if ($stmt = $mysqli->prepare("SELECT id_sub_menu, name_sub_menu, main, ord, link, permaname FROM sub_menu WHERE name_menu = ? AND main IS NULL ORDER BY ord")) {
				$stmt->bind_param('s', $name_widget); 
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($id_sub_menu, $name_sub_menu, $main, $ord, $link, $permaname); 
				if ($stmt->affected_rows > 0) {
					while ($stmt->fetch()) {
						array_push($this->sub_menu, new Submenu($id_sub_menu, $name_widget, $name_sub_menu, $main, $ord, $link, $permaname, $mysqli));
					}
				}
			}
		}
	}
}
