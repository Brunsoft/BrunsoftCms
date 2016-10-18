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
				
	elseif(isset($_POST['name_menu']) && isset($_POST['id_submenu_sel'])){
		if(checkData()){
			if(searchMenu($_POST['name_menu'], $mysqli) == 2){
				if(searchSubMenu($_POST['name_menu'], $_POST['id_submenu_sel'], $mysqli) == 2){
					if(deleteSubMenuSel($_POST['id_submenu_sel'], $mysqli)){
						resetVar();
						unset($_SESSION['message']);
						header('Location: gest-menu.php');	
						$_SESSION['message'] = "<er>Sottovoce eliminata con successo</er>";
					}else
						$_SESSION['message'] = "<er>Errore durante la cancellazione dei sottomenu</er>";
				}else
					$_SESSION['message'] = "<er>Non esiste alcun submenu con questo id</er>";
			}else
				$_SESSION['message'] = "<er>Non esiste alcun menu con questo nome</er>";
		}
	}else
		$_SESSION['message'] = "<er>Richiesta non valida</er>";

	header('Location: gest-menu.php');
	
	function checkData(){
		if(strlen($_POST['name_menu']) > 50 || strlen($_POST['name_menu']) == 0){
			$_SESSION['message'] = "<er>Valore nome menu selezionato non corretto</er>";
			return false;
		}else
			return true;
	}
	
	function resetVar(){
		unset($_POST['name_menu']);
		unset($_POST['id_submenu_sel']);
	}
?>