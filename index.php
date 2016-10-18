<?php
require_once('model.php');
require_once('view.php');
require_once('controller.php');
require('src/database/db_connect.php');
require('src/login/function_login.php');
require('src/articoli/function_article.php');

sec_session_start();
$model = new Model($mysqli);
$controller = new Controller($model);
$view = new View($controller, $model);

// inserisco commenti sotto gli articoli vulnerabilità XSS
if(isset($_POST['html_article']) && isset($_POST['name_article']) && login_check($mysqli) && isAdmin($mysqli) > 0){
	if(checkData()){
		$html_article = filter_var($_POST['html_article'], FILTER_SANITIZE_STRING); 	// ci proteggiamo da un attacco XSS
		$name_article = filter_var($_POST['name_article'], FILTER_SANITIZE_STRING); 	// ci proteggiamo da un attacco XSS.
		//$html_article = $_POST['html_article'];			// nessun controllo
		//$name_article = $_POST['name_article'];			// nessun controllo
		
		if(searchArticle($name_article, $mysqli) == 1)
			insertComment($name_article, $html_article, $mysqli);
	}
}

// eseguo il login da widget vulnerabilità SQL Injection
if(isset($_POST['email_widget']) && isset($_POST['pwd_widget'])){
	echo login_widget($_POST['email_widget'], $_POST['pwd_widget'], $mysqli);
}

// ricavo pagina locale
$local_page = str_ireplace(ROOT, "", $_SERVER['REQUEST_URI']);

if($local_page == ''){
	$controller->getPage("home");
}else{
	if(!$controller->getPage($local_page)){
		echo pageNotFound();
		return;
	}
}
	
echo $view->output();

function checkData(){
	if(strlen($_POST['name_article']) > 50 || strlen($_POST['name_article']) == 0){
		$_SESSION['message'] = "<er>Valore nome articolo non corretto</er>";
		return false;
	}elseif(strlen($_POST['html_article']) > 2048){
		$_SESSION['message'] = "<er>Valore contenuto articolo non corretto</er>";
		return false;
	}else
		return true;
}

function pageNotFound(){
	$result = '
        <!DOCTYPE HTML>
		<html>
			<head>
				<title>Intensify by TEMPLATED</title>
				<meta charset="utf-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<link rel="stylesheet" href="'.ROOT.'assets/css/main.css" />
			</head>
			<body>
				<section class="wrapper">
					<div class="inner" style="text-align: center; padding-top: 30px;">
						<span class="image fit">
							<img src="'.ROOT.'images/404.jpg" alt="404 - Pagina non trovata" style="height: 210px; width: 200px; margin: 0 auto;">
							</span>
							<h3>Pagina non trovata</h3>
							<h4>L\'URL che hai digitato non è stato trovato sul server.</h4>
							<p>Torna alla <a href="'.ROOT.'">HOME</a>.</p>
							<p>Redirect automatico tra 5 secondi...</p>
						</div>
					
				</section>
			</body>
			<script>
    			window.setTimeout(function(){
					window.location.href = "'.ROOT.'";
				}, 5000);
			</script>
		</html>
		';
	return $result;
}
?>