<?php
	function getPages($mysqli){
		$pages = array();
		if ($stmt = $mysqli->prepare("SELECT name_page FROM page")) {
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_page); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($pages, new Page($name_page, $mysqli));
				}
			}
		}
		return $pages;
	}
	
	function getNoMenuPages($name_menu, $check_pages, $mysqli){
		$uncheck_pages = array();
		if ($stmt = $mysqli->prepare("SELECT name_page FROM page WHERE name_page in (SELECT name_page FROM widget_page WHERE name_widget = ?) OR name_page not in (SELECT name_page FROM widget_page)")) {
			$stmt->bind_param('s', $name_menu);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_page); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					if(!in_array($name_page, $check_pages))
						array_push($uncheck_pages, new Page($name_page, $mysqli));
				}
			}
		}
		return $uncheck_pages;
	}
	
	function searchPage($name, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM page WHERE name_page = ?")) {
			$stmt->bind_param('s', $name);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 la pagina non esiste
				return 1;
			elseif($stmt->affected_rows == 1)		// 2 la pagina esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	function findPagePermaName($name_page, $mysqli){
		$name = strtolower(str_replace(" ","-",$name_page));
		if(findEqualPermaname($name, $mysqli) == 2){
			$i = 1;
			while(findEqualPermaname($name.'-'.$i, $mysqli) == 2){
				$i++;
			}
			$name = $name.'-'.$i;
		}
		return $name;
	}
	
	function findEqualPermaname($permaname, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM page WHERE permaname = ?")) {
			$stmt->bind_param('s', $permaname);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 la pagina non esiste
				return 1;
			elseif($stmt->affected_rows >= 1)		// 2 la pagina esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	
	function insertPage($name, $title, $descr, $type, $public, $permaname, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];
	    if ($stmt = $mysqli->prepare("INSERT INTO page (name_page, title, descr, type, date_creation, user_author, published, permaname) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
			$stmt->bind_param('sssssiis', $name, $title, $descr, $type, $data, $id_user, $public, $permaname);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function updatePage($old_name, $name, $title, $descr, $type, $public, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];
	    if ($stmt = $mysqli->prepare("UPDATE page SET name_page = ?, title = ?, descr = ?, type = ?, date_last_edit = ?, user_last_edit = ?, published = ? WHERE name_page = ?")) {
			$stmt->bind_param('sssssiis', $name, $title, $descr, $type, $data, $id_user, $public, $old_name);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function deletePage($name, $mysqli) {		
		if ($stmt = $mysqli->prepare("DELETE FROM page WHERE name_page = ?")) {
			$stmt->bind_param('s', $name); 	
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
?>