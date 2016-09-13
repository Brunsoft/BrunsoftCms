<?php
class Logo{
    private $name_logo;
    private $link;

    public function __construct(){
        $this->name_logo = '';
        $this->link = '';
    }
    
    public function init($name_logo, $database){
		if ($stmt = $database->mysqli->prepare("SELECT link FROM logo WHERE name_logo = ?")) {
			$stmt->bind_param('s', $name_logo); 
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($link); 
			if ($stmt->affected_rows > 0) {
				$stmt->fetch();
				$this->name_logo = $name_logo;
				$this->link = $link;
			}
	   	}
	}
		
	public function toString(){
		$result = "<a href=\"".$this->link."\" class=\"logo\">".$this->name_logo."</a>";
		return $result;
	}

}
