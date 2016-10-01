<?php
	function getArticles($mysqli){
		$articles = array();
		if ($stmt = $mysqli->prepare("SELECT name_article FROM article")) {
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_article); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($articles, new Article($name_article, $mysqli));
				}
			}
		}
		return $articles;
	}
	
	function getArticlesCatigory($category, $mysqli){
		$articles = array();
		if ($stmt = $mysqli->prepare("SELECT name_article FROM article WHERE category = ?")) {
			$stmt->bind_param('s', $category);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_article); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($articles, $name_article);
				}
			}
		}
		return $articles;
	}	
	
	function getCategories($mysqli){
		$categories = array();
		if ($stmt = $mysqli->prepare("SELECT name_category FROM article_category")) {
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_category); 
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($categories, $name_category);
				}
			}
		}
		return $categories;
	}
	
	function searchArticle($name, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM article WHERE name_article = ?")) {
			$stmt->bind_param('s', $name);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 l'articolo non esiste
				return 1;
			elseif($stmt->affected_rows == 1)		// 2 l'articolo esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	function searchCategory($name, $mysqli){
		if ($stmt = $mysqli->prepare("SELECT * FROM article_category WHERE name_category = ?")) {
			$stmt->bind_param('s', $name);
			$stmt->execute();
			$stmt->store_result(); 
			if ($stmt->affected_rows == 0)			// 1 la categoria non esiste
				return 1;
			elseif($stmt->affected_rows == 1)		// 2 la categoria esiste
				return 2;
	   }
	   return 0;			// errore
	}
	
	function insertArticle($name, $category, $html, $public, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];

	    if ($stmt = $mysqli->prepare("INSERT INTO article (name_article, category, html, date_creation, user_author, published) VALUES (?, ?, ?, ?, ?, ?)")) {
			$stmt->bind_param('ssssii', $name, $category, $html, $data, $id_user, $public);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows > 0) 
				return true;
	   	}
	   	return false;
	}
	
	function updateArticle($old_name, $name, $category, $html, $public, $mysqli) {		
	    $data = date('Y-m-d H:i:s');
	    $id_user = (int)$_SESSION['user_id'];

	    if ($stmt = $mysqli->prepare("UPDATE article SET name_article = ?, category = ?, html = ?, date_last_edit = ?, user_last_edit = ?, published = ? WHERE name_article = ?")) {
			$stmt->bind_param('ssssiis', $name, $category, $html, $data, $id_user, $public, $old_name);
			$stmt->execute(); 
			$stmt->store_result();
			if ($stmt->affected_rows >= 0) 
				return true;
	   	}
	   	return false;
	}

?>