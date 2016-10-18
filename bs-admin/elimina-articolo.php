<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/articoli/function_article.php");
	require("../src/class/article.php");
	sec_session_start();
	
	if(!login_check($mysqli))
		header('Location: ../bs_login.php');
	if(isAdmin($mysqli) != 2)
		header('Location: ../');
				
	elseif(isset($_POST['name_article_sel'])){
		if(searchArticle($_POST['name_article_sel'], $mysqli) == 2){
			if(deleteArticle($_POST['name_article_sel'], $mysqli)){
				resetVar();
				unset($_SESSION['message']);
				header('Location: gest-articoli.php');	
			}else
				$_SESSION['message'] = "<er>Errore durante la cancellazione</er>";
		}else
			$_SESSION['message'] = "<er>Non esiste alcun articolo con questo nome</er>";
	}else
		unset($_SESSION['message']);
		
	header('Location: gest-articoli.php');
		
	function resetVar(){
		unset($_POST['name_article_sel']);
	}
?>