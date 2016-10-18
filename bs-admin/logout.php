<?php
	include '../src/login/function_login.php';
	sec_session_start();						
	$_SESSION = array();						// Elimina tutti i valori della sessione.
	$params = session_get_cookie_params();		// Recupera i parametri di sessione.
	// Cancella i cookie attuali.
	setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	session_destroy();							// Cancella la sessione.
	header('Location: ../');
?>