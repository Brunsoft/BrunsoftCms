<?php
	function getPages($mysqli){
		$pages = array();
		if ($stmt = $mysqli->prepare("SELECT id_page FROM page")) {
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id_page); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($pages, new Page($id_page, $mysqli));
				}
			}
		}
		return $pages;
	}
	
	function getNoMenuPages($name_menu, $check_pages, $mysqli){
		$uncheck_pages = array();
		if ($stmt = $mysqli->prepare("SELECT id_page FROM page WHERE id_page in (SELECT id_page FROM widget_page WHERE name_widget = ?) OR id_page not in (SELECT id_page FROM widget_page)")) {
			$stmt->bind_param('s', $name_menu);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id_page); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					if(!in_array($id_page, $check_pages))
						array_push($uncheck_pages, new Page($id_page, $mysqli));
				}
			}
		}
		return $uncheck_pages;
	}
	
	function getNameArticlePageSel($id_page, $mysqli){
		$result = "";
		if ($stmt = $mysqli->prepare("SELECT name_article FROM article_page WHERE id_page = ?")) {
			$stmt->bind_param('i', $id_page);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_article); 
			$stmt->fetch();
			if ($stmt->affected_rows > 0){
				$result = $name_article;
			}
		}
		return $result;
	}
	
	function searchPage($id_page, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM page WHERE id_page = ?")) {
			$stmt->bind_param('i', $id_page);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 la pagina non esiste
				return 1;
			elseif($stmt->affected_rows == 1)		// 2 la pagina esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	function searchNamePage($name_page, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM page WHERE name_page = ?")) {
			$stmt->bind_param('s', $name_page);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 la pagina non esiste
				return 1;
			elseif($stmt->affected_rows == 1)		// 2 la pagina esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	function searchPageName($name_page, $id_page, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM page WHERE name_page = ? AND id_page != ?")) {
			$stmt->bind_param('si', $name_page, $id_page);
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
	
	
	function insertPage($name, $title, $descr, $type, $public, $main, $permaname, $category, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];
	    $ord = 0;
	    if ($stmt = $mysqli->prepare("INSERT INTO page (name_page, title, descr, type, date_creation, user_author, published, permaname, main_page, category_articles, ord) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
			$stmt->bind_param('sssisiisisi', $name, $title, $descr, $type, $data, $id_user, $public, $permaname, $main, $category, $ord);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function updatePage($id_page, $name, $title, $descr, $type, $public, $main_page, $permaname, $category, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];
	    if ($stmt = $mysqli->prepare("UPDATE page SET name_page = ?, title = ?, descr = ?, type = ?, date_last_edit = ?, user_last_edit = ?, published = ?, main_page = ?, permaname = ?, category_articles = ? WHERE id_page = ?")) {
			$stmt->bind_param('sssssiiissi', $name, $title, $descr, $type, $data, $id_user, $public, $main_page, $permaname, $category, $id_page);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function getUsablePage($id_page, $main_page, $main, $step, $mysqli){
		$result = "";
		if($step == 0){
			if ($stmt = $mysqli->prepare("SELECT id_page, name_page FROM page WHERE main_page IS NULL ORDER BY ord")) {
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($id_page_sel, $name_page_sel); 
				if ($stmt->affected_rows > 0){
					while ($stmt->fetch()) {
						if(strcmp($id_page, $id_page_sel) != 0){	
							$result .= '<option value="'.$id_page_sel.'"';
							if(strcmp($main_page, $id_page_sel) == 0 && $main_page != null)
								$result .= ' selected=""';
							$result .= '>'.$name_page_sel.'</option>';
							$result .= getUsablePage($id_page, $main_page, $id_page_sel, $step+1, $mysqli);
						}
					}
				}
			}
		}else{
			if ($stmt = $mysqli->prepare("SELECT id_page, name_page FROM page WHERE main_page = ? ORDER BY ord")) {
				$stmt->bind_param('s', $main);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($id_page_sel, $name_page_sel); 
				if ($stmt->affected_rows > 0){
					while ($stmt->fetch()) {
						if(strcmp($id_page, $id_page_sel) != 0){	
							$result .= '<option value="'.$id_page_sel.'"';
							if(strcmp($main_page, $id_page_sel) == 0 && $main_page != null)
								$result .= ' selected=""';
							$result .= '>'.$name_page_sel.'</option>';
							$result .= getUsablePage($id_page, $main_page, $id_page_sel, $step+1, $mysqli);
						}
					}
				}
			}
		}
		return $result;
		
	}
	
	function getIdPage($name_page, $mysqli){
		if ($stmt = $mysqli->prepare()) {
			$stmt->bind_param('s', $name_page); 	
			$stmt->execute(); 
			$stmt->store_result();
			$stmt->bind_result($id_page); 
			$stmt->fetch();
			if ($stmt->affected_rows > 0) 
				return $id_page;
	   	}
	   	return "";
	}
	
	function searchPagePermaname($permaname, $main_permaname, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM page WHERE permaname = ? AND main_page = (SELECT id_page FROM page WHERE permaname = ?)")) {
			$stmt->bind_param('ss', $permaname, $main_permaname); 	
			$stmt->execute(); 
			$stmt->store_result();
			$stmt->fetch();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function getIdPagePermaname($permaname_page, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT id_page FROM page WHERE permaname = ?")) {
			$stmt->bind_param('s', $permaname_page); 	
			$stmt->execute(); 
			$stmt->store_result();
			$stmt->bind_result($id_page); 
			$stmt->fetch();
			if ($stmt->affected_rows > 0) 
				return $id_page;
	   	}
	   	return 0;
	}
		
	function deletePage($id_page, $mysqli) {		
		if ($stmt = $mysqli->prepare("DELETE FROM page WHERE id_page = ?")) {
			$stmt->bind_param('i', $id_page); 	
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function deleteArticlePage($id_page, $mysqli) {		
		if ($stmt = $mysqli->prepare("DELETE FROM article_page WHERE id_page = ?")) {
			$stmt->bind_param('i', $id_page); 	
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows >= 0) 
				return true;
	   	}
	   	return false;
	}
	
	function insertArticlePage($id_page, $name_article, $mysqli) {		

	    if ($stmt = $mysqli->prepare("INSERT INTO article_page (id_page, name_article) VALUES (?, ?)")) {
			$stmt->bind_param('is', $id_page, $name_article);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}	
?>