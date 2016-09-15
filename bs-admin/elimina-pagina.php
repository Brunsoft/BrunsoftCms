<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/pagine/function_page.php");
	require("../src/class/page.php");
	sec_session_start();
	
	if(!login_check($mysqli))
		header('Location: ../bs_login.php');
				
	elseif(isset($_POST['name_page_sel'])){
		if(checkData()){
			if(searchPage($_POST['name_page_sel'], $mysqli) == 2){
				if(deletePage($_POST['name_page_sel'], $mysqli)){
					resetVar();
					unset($_SESSION['message']);
					header('Location: gest-pagine.php');	
				}else
					$_SESSION['message'] = "<er>Errore durante l'update</er>";
			}else
				$_SESSION['message'] = "<er>Non esiste alcuna pagina con questo nome</er>";
		}
	}else
		unset($_SESSION['message']);
		
	header('Location: gest-pagine.php');
	
	function checkData(){
		if(strlen($_POST['name_page_sel']) > 50 || strlen($_POST['name_page_sel']) == 0){
			$_SESSION['message'] = "<er>Valore nome pagina selezionata non corretto</er>";
			return false;
		}else
			return true;
	}
	
	function resetVar(){
		unset($_POST['name_page_sel']);
	}
?>