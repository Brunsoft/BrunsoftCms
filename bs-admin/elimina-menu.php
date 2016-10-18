<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/menu/function_menu.php");
	require("../src/class/widget.php");
	sec_session_start();
	
	if(!login_check($mysqli))
		header('Location: ../bs_login.php');
	if(isAdmin($mysqli) != 2)
		header('Location: ../');
				
	elseif(isset($_POST['name_menu_sel'])){
		if(checkData()){
			if(searchMenu($_POST['name_menu_sel'], $mysqli) == 2){
				if(deleteSubMenu($_POST['name_menu_sel'], $mysqli)){
					if(deleteMenu($_POST['name_menu_sel'], $mysqli)){
						resetVar();
						unset($_SESSION['message']);
						header('Location: gest-menu.php');	
					}else
						$_SESSION['message'] = "<er>Errore durante la cancellazione</er>";
				}else
					$_SESSION['message'] = "<er>Errore durante la cancellazione dei sottomenu</er>";
			}else
				$_SESSION['message'] = "<er>Non esiste alcun menu con questo nome</er>";
		}
	}else
		unset($_SESSION['message']);
		
	header('Location: gest-menu.php');
	
	function checkData(){
		if(strlen($_POST['name_menu_sel']) > 50 || strlen($_POST['name_menu_sel']) == 0){
			$_SESSION['message'] = "<er>Valore nome menu selezionato non corretto</er>";
			return false;
		}else
			return true;
	}
	
	function resetVar(){
		unset($_POST['name_menu_sel']);
	}
?>