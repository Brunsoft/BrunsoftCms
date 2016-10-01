<?php
	function getWidgets($pos, $mysqli){
		$widgets = array();
		if ($stmt = $mysqli->prepare("SELECT name_widget FROM widget WHERE pos = ?")) {
			$stmt->bind_param('s', $pos);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_widget); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($widgets, new Widget($name_widget, $mysqli));
				}
			}
		}
		return $widgets;
	}
	
	function getWidgetPages($name_widget, $mysqli){
		$pages = array();
		if ($stmt = $mysqli->prepare("SELECT id_page FROM widget_page WHERE name_widget = ?")) {
			$stmt->bind_param('s', $name_widget);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id_page); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($pages, $id_page);
				}
			}
		}
		return $pages;
	}
	
	function searchWidget($name, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM widget WHERE name_widget = ?")) {
			$stmt->bind_param('s', $name);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 il widget non esiste
				return 1;
			elseif($stmt->affected_rows == 1)		// 2 il widget esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	function insertWidget($name, $html, $ord, $pos, $dim, $public, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];
	    $type = "html";
	    switch($pos){
			case 1:
				$pos = 'banner';
				break;
			case 2:
				$pos = 'topA';
				break;
			case 3:
				$pos = 'topB';
				break;
			case 4:
				$pos = 'bottomA';
				break;
			case 5:
				$pos = 'bottomB';
				break;
			case 6:
				$pos = 'footer';
				break;
			default:
				return false;
		}

	    if ($stmt = $mysqli->prepare("INSERT INTO widget (name_widget, html, ord, pos, type, dim, date_creation, user_author, published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
			$stmt->bind_param('ssissisii', $name, $html, $ord, $pos, $type, $dim, $data, $id_user, $public);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function deleteWidget($name_widget, $mysqli) {		
	    if ($stmt = $mysqli->prepare("DELETE FROM widget WHERE name_widget = ?")) {
			$stmt->bind_param('s', $name_widget);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function updateWidget($old_name, $name, $html, $ord, $pos, $dim, $public, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];
	    $type = "html";
	    switch($pos){
			case 1:
				$pos = 'banner';
				break;
			case 2:
				$pos = 'topA';
				break;
			case 3:
				$pos = 'topB';
				break;
			case 4:
				$pos = 'bottomA';
				break;
			case 5:
				$pos = 'bottomB';
				break;
			case 6:
				$pos = 'footer';
				break;
			default:
				return false;
		}

	    if ($stmt = $mysqli->prepare("UPDATE widget SET name_widget = ?, html = ?, ord = ?, pos = ?, type = ?, dim = ?, date_last_edit = ?, user_last_edit = ?, published = ? WHERE name_widget = ?")) {
			$stmt->bind_param('ssissisiis', $name, $html, $ord, $pos, $type, $dim, $data, $id_user, $public, $old_name);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function insertWidgetPage($id_page, $name_widget, $mysqli) {		
	    if ($stmt = $mysqli->prepare("INSERT INTO widget_page (id_page, name_widget) VALUES (?, ?)")) {
			$stmt->bind_param('is', $id_page, $name_widget);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function deleteWidgetPage($name_widget, $mysqli) {		
	    if ($stmt = $mysqli->prepare("DELETE FROM widget_page WHERE name_widget = ?")) {
			$stmt->bind_param('s', $name_widget);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows >= 0) 
				return true;
	   	}
	   	return false;
	}
	
?>