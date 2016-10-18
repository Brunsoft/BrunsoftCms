<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/pagine/function_page.php");
	require("../src/class/page.php");
	sec_session_start();
	
	if(!login_check($mysqli))
		header('Location: ../bs_login.php');
	if(isAdmin($mysqli) != 2)
		header('Location: ../');
				
	elseif(isset($_POST['id_page_sel'])){
		if(searchPage($_POST['id_page_sel'], $mysqli) == 2){
			if(deletePage($_POST['id_page_sel'], $mysqli)){
				resetVar();
				unset($_SESSION['message']);
				header('Location: gest-pagine.php');	
			}else
				$_SESSION['message'] = "<er>Errore durante l'update</er>";
		}else
			$_SESSION['message'] = "<er>Non esiste alcuna pagina con questo nome</er>";
	}else
		unset($_SESSION['message']);
		
	header('Location: gest-pagine.php');
		
	function resetVar(){
		unset($_POST['id_page_sel']);
	}
?>