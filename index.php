<?php
require_once('model.php');
require_once('view.php');
require_once('controller.php');
require('src/database/db_connect.php');

$model = new Model($mysqli);
$controller = new Controller($model);
$view = new View($controller, $model);

// ricavo pagina locale
$local_page = str_ireplace("/mvc/", "", $_SERVER['REQUEST_URI']);

if($local_page == ''){
	$controller->getPage("home");
}else{
	$controller->getPage($local_page);
}
	
echo $view->output();
?>