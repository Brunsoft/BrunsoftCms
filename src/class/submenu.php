<?php
class Submenu{
	public $id_sub_menu;
    public $name_menu;
    public $name_sub_menu;
    public $main;
    public $ord;
    public $link;
    public $permaname;
    public $sub_menu;

    public function __construct($id_sub_menu, $name_menu, $name_sub_menu, $main, $ord, $link, $permaname, $mysqli){
    	$this->id_sub_menu = $id_sub_menu;
        $this->name_menu = $name_menu;
        $this->name_sub_menu = $name_sub_menu;
        $this->main = $main;
        $this->ord = $ord;
        $this->link = $link;
        $this->permaname = $permaname;
        $this->sub_menu = array();
        $this->init_submenu($name_menu, $id_sub_menu, $mysqli);
    }
    
    public function init_submenu($name_menu, $main, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT id_sub_menu, name_sub_menu, main, ord, link, permaname FROM sub_menu WHERE name_menu = ? AND main = ? ORDER BY ord")) {
			$stmt->bind_param('si', $name_menu, $main); 
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id_sub_menu, $name_sub_menu, $main, $ord, $link, $permaname); 
			if ($stmt->affected_rows > 0) {
				while ($stmt->fetch()) {
					array_push($this->sub_menu, new Submenu($id_sub_menu, $name_menu, $name_sub_menu, $main, $ord, $link, $permaname, $mysqli));
				}
			}
		}
	}
		
	public function toString(){
		$result = "<a href=\"".$this->link."\">".$this->name_sub_menu."</a>";
		foreach ($this->sub_menu as $voce){
			$result .= $voce->toString();
		}
		return $result;
	}

}
