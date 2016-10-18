<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/widget/function_widget.php");
	require("../src/class/widget.php");
	sec_session_start();
	
	if(!login_check($mysqli))
		header('Location: ../bs_login.php');
	if(isAdmin($mysqli) != 2)
		header('Location: ../');
				
	elseif(isset($_POST['name_widget_sel'])){
		if(checkData()){
			if(searchwidget($_POST['name_widget_sel'], $mysqli) == 2){
				if(deleteWidget($_POST['name_widget_sel'], $mysqli)){
					resetVar();
					header('Location: gest-widget.php');	
				}else
					$_SESSION['message'] = "<er>Errore durante la cancellazione</er>";
			}else
				$_SESSION['message'] = "<er>Non esiste alcun widget con questo nome</er>";
		}
	}else
		unset($_SESSION['message']);
		
	header('Location: gest-widget.php');
	
	function checkData(){
		if(strlen($_POST['name_widget_sel']) > 50 || strlen($_POST['name_widget_sel']) == 0){
			$_SESSION['message'] = "<er>Valore nome widget selezionato non corretto</er>";
			return false;
		}else
			return true;
	}
	
	function resetVar(){
		unset($_POST['name_widget_sel']);
	}
?>