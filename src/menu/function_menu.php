<?php
	function getMenus($mysqli){
		$menus = array();
		if ($stmt = $mysqli->prepare("SELECT name_widget FROM widget WHERE type = 'menu'")) {
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_menu); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($menus, new Widget($name_menu, $mysqli));
				}
			}
		}
		return $menus;
	}
	
	function getPagesMenu($name_menu, $mysqli){
		$pages = array();
		if ($stmt = $mysqli->prepare("SELECT name_page FROM widget_page WHERE name_widget = ?")) {
			$stmt->bind_param('s', $name_menu);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_page); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($pages, $name_page);
				}
			}
		}
		return $pages;
	}
	
	function countSubmenu($name_menu, $main_submenu, $mysqli){
		$result = 0;
		if($main_submenu == 0){		// sottovoce principale main IS NULL
			if ($stmt = $mysqli->prepare("SELECT count(*) FROM sub_menu WHERE name_menu = ? AND main IS NULL")) {
				$stmt->bind_param('s', $name_menu);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($count); 
				$stmt->fetch();
				$result = $count;
			}
		}else{						// sottovoce principale main = sottovoce esistente
			if ($stmt = $mysqli->prepare("SELECT count(*) FROM sub_menu WHERE name_menu = ? AND main = ?")) {
				$stmt->bind_param('si', $name_menu, $main_submenu);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($count); 
				$stmt->fetch();
				$result = $count;
			}
		}
		return $result;
	}
	
	function searchMenu($name, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM widget WHERE name_widget = ? AND type = 'menu'")) {
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
	
	function searchSubMenu($name_menu, $id_submenu, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM sub_menu WHERE id_sub_menu = ? AND name_menu = ?")) {
			$stmt->bind_param('is', $id_submenu, $name_menu);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 la pagina non esiste
				return 1;
			elseif($stmt->affected_rows == 1)		// 2 la pagina esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	function findPermaName($name_menu, $name_submenu, $mysqli){
		$name = strtolower(str_replace(" ","-",$name_submenu));
		if(findEqualSubMenu($name_menu, $name, $mysqli) == 2){
			$i = 1;
			while(findEqualSubMenu($name_menu, $name.'-'.$i, $mysqli) == 2){
				$i++;
			}
			$name = $name.'-'.$i;
		}
		return $name;
	}
	
	function findEqualSubMenu($name_menu, $permaname, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM sub_menu WHERE name_menu = ? AND permaname = ?")) {
			$stmt->bind_param('ss', $name_menu, $permaname);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 la pagina non esiste
				return 1;
			elseif($stmt->affected_rows >= 1)		// 2 la pagina esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	function getSubMenuSel($name_menu, $id_sub_menu, $mysqli){
		$sub_menu = "";
		if ($stmt = $mysqli->prepare("SELECT name_sub_menu, main, ord, link, permaname FROM sub_menu WHERE id_sub_menu = ? AND name_menu = ?")) {
			$stmt->bind_param('is', $id_sub_menu, $name_menu);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_sub_menu, $main, $ord, $link, $permaname); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					$sub_menu = new Submenu($id_sub_menu, $name_menu, $name_sub_menu, $main, $ord, $link, $permaname, $mysqli);
				}
			}
		}
		return $sub_menu;
	}
	
	function insertMenu($name, $pos, $type, $public, $mysqli) {
		$ord = 1;
		$dim = 1;		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];
	    if ($stmt = $mysqli->prepare("INSERT INTO widget (name_widget, ord, pos, type, dim, date_creation, user_author, published) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
			$stmt->bind_param('sissisii', $name, $ord, $pos, $type, $dim, $data, $id_user, $public);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function insertPageMenu($name_page, $name_menu, $mysqli) {
		if ($stmt = $mysqli->prepare("INSERT INTO widget_page (name_page, name_widget) VALUES (?, ?)")) {
			$stmt->bind_param('ss', $name_page, $name_menu);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function updateMenu($old_name, $name, $pos, $public, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];
	    if ($stmt = $mysqli->prepare("UPDATE widget SET name_widget = ?, pos = ?, date_last_edit = ?, user_last_edit = ?, published = ? WHERE name_widget = ?")) {
			$stmt->bind_param('sssiis', $name, $pos, $data, $id_user, $public, $old_name);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function updateSubmenu($id_submenu_sel, $name_submenu, $name_menu, $main_submenu, $ord_submenu, $link_submenu, $mysqli) {
		if($main_submenu == 0)
			$main_submenu = null;
			
	   	if ($stmt = $mysqli->prepare("UPDATE sub_menu SET name_sub_menu = ?, main = ?, ord = ?, link = ? WHERE id_sub_menu = ? AND name_menu = ?")) {
			$stmt->bind_param('siisis', $name_submenu, $main_submenu, $ord_submenu, $link_submenu, $id_submenu_sel, $name_menu);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows >= 0) 
				return true;
	   	}
	   	return false;
	}
	
	function insertSubmenu($name_submenu, $name_menu, $main_submenu, $ord_submenu, $link_submenu, $permaname_submenu, $mysqli) {
		if($main_submenu == 0)
			$main_submenu = null;
			
	   	if ($stmt = $mysqli->prepare("INSERT INTO sub_menu (name_menu, name_sub_menu, main, ord, link, permaname) VALUES(?, ?, ?, ?, ?, ?)")) {
			$stmt->bind_param('ssiiss', $name_menu, $name_submenu, $main_submenu, $ord_submenu, $link_submenu, $permaname_submenu);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows >= 0) 
				return true;
	   	}
	   	return false;
	}
	
	function deletePageMenu($name_menu, $mysqli){
		if ($stmt = $mysqli->prepare("DELETE FROM widget_page WHERE name_widget = ?")) {
			$stmt->bind_param('s', $name_menu); 	
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows >= 0) 
				return true;
	   	}
	   	return false;
	}
	
	function deleteMenu($name_menu, $mysqli){
		if ($stmt = $mysqli->prepare("DELETE FROM widget WHERE name_widget = ?")) {
			$stmt->bind_param('s', $name_menu); 	
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows >= 0) 
				return true;
	   	}
	   	return false;
	}
	
	function deleteSubMenu($name_menu, $mysqli){
		if ($stmt = $mysqli->prepare("DELETE FROM sub_menu WHERE name_menu = ?")) {
			$stmt->bind_param('s', $name_menu); 	
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows >= 0) 
				return true;
	   	}
	   	return false;
	}
	
	function deleteSubMenuSel($id_sub_menu, $name_menu, $mysqli){
		if ($stmt = $mysqli->prepare("DELETE FROM sub_menu WHERE id_sub_menu = ? AND name_menu = ?")) {
			$stmt->bind_param('is', $id_sub_menu, $name_menu); 	
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function printSubMenu($list_sub_menu, $id_submenu_sel, $liv, $mysqli){
		$result = "";
		foreach($list_sub_menu as $sub_menu){
			if($sub_menu->id_sub_menu == $id_submenu_sel)
				$result .= '<div class="menu-table select-menu" style="margin-left:'.$liv.'px;"><div clsss="3u">';
			else
				$result .= '<div class="menu-table" style="margin-left:'.$liv.'px;"><div clsss="3u">';
			
			$result .= '<form action="visualizza-menu.php" method="post" >';
			$result .= "<input type=\"hidden\" name=\"id_submenu_sel\" value=\"".$sub_menu->id_sub_menu."\" />";
			$result .= "<input type=\"hidden\" name=\"name_menu\" value=\"".$sub_menu->name_menu."\" />";
			$result .= "<button class=\"mod-del-info edit\" name=\"Modifica\" title=\"Modifica\" type=\"submit\" ></button></form>";
			$result .= '<form action="visualizza-menu.php" method="post" >';
			$result .= "<input type=\"hidden\" name=\"id_submenu_sel\" value=\"".$sub_menu->id_sub_menu."\" />";
			$result .= "<input type=\"hidden\" name=\"name_menu\" value=\"".$sub_menu->name_menu."\" />";
			$result .= "<button class=\"mod-del-info delete\" name=\"elimina\" title=\"Elimina\" type=\"submit\" onclick=\"return confirm('Sicuro di voler elliminare la voce selezionata e tutte le sue sottovoci?')\" ></button></form>";
			$result .= '</div><div clsss="9u"><mt>'.$sub_menu->name_sub_menu.'</mt></div>';
			$result .= '</div>';
			if(!empty($sub_menu->sub_menu))
				$result .= printSubMenu($sub_menu->sub_menu, $id_submenu_sel, $liv+30, $mysqli);
		}
		return $result;
	}
	
	function printSubMenu1($list_sub_menu, $main_submenu, $liv, $mysqli){
		$result = "";
		foreach($list_sub_menu as $sub_menu){
			$result .= '<option value="'.$sub_menu->id_sub_menu.'"';
			if($sub_menu->id_sub_menu == $main_submenu)
				$result .= ' selected=""';
				
			$result .= '>';
			for($i=0; $i<$liv; $i++)
				$result .= '- ';
			$result .= $sub_menu->name_sub_menu.'</option>';
			if(!empty($sub_menu->sub_menu) && $sub_menu->id_sub_menu != $main_submenu)
				$result .= printSubMenu1($sub_menu->sub_menu, $main_submenu, $liv+1, $mysqli);
		}
		return $result;
		
	}
?>