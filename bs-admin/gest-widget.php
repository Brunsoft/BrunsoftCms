<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/widget/function_widget.php");
	require("../src/class/widget.php");
	sec_session_start();
	
	if(!login_check($mysqli))
		header('Location: ../bs-login.php');
	if(isAdmin($mysqli) != 2)
		header('Location: ../');
		
?>
<!DOCTYPE HTML>
<!--
	Theory by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
<html>
	<head>
		<title>Gestione Widget</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="../assets/css/main.css" />
	</head>
	<body class="subpage">

		<!-- Header -->
		<header id="header">
			<div class="inner">
				<a href="<?php echo ROOT; ?>" class="logo">Brunsoft</a>
				<nav id="nav">
					<a href="gest-pagine.php">Pagine</a>
					<a href="gest-menu.php">Menu</a>
					<a href="gest-articoli.php">Articoli</a>
					<a href="gest-widget.php">Widget</a>
					<a href="logout.php" >Logout</a>
				</nav>
				<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
			</div>
		</header>

		<!-- Three -->
		<section id="three" class="wrapper">
			<div class="inner">
				<header class="align-center">
					<h2>Gestione Widget</h2>
					<?php 
					if(isset($_SESSION['message'])) 
						echo $_SESSION['message']; 
					else
						echo "<p>Crea, modifica elimina widget.</p>";	
					?>
				</header>
				<?php
					$array_pos = array("banner","topA","topB","bottomA","bottomB","footer");
					for($i=0; $i<count($array_pos); $i++){
						$widgets = getWidgets($array_pos[$i], "html", $mysqli);
						if(!empty($widgets)){
							$result = '<h3>Posizione: '.$array_pos[$i].'</h3>';
							$result .= '<table><thead><tr>';
							$result .= '<th>Azioni</th><th>Stato</th><th>Nome</th><th>Data Creazione</th><th>Autore</th>';
							$result .= '</tr></thead>';
							foreach($widgets as $widget){
								$result .= '<tr><td><form action="modifica-widget.php" method="post" >';
					  			$result .= '<input type="hidden" name="name_widget_sel" value="'.$widget->name_widget.'" />';
					  			$result .= '<input type="hidden" name="type_widget" value="'.$widget->type.'" />';
					  			$result .= '<button class="mod-del-info edit" name="Modifica" title="Modifica" type="submit" ></button>';
					  			$result .= '</form>';
					  			$result .= '<form action="elimina-widget.php" method="post" >';
					  			$result .= '<input type="hidden" name="name_widget_sel" value="'.$widget->name_widget.'" />';
					  			$result .= '<input type="hidden" name="type_widget" value="'.$widget->type.'" />';
					  			$result .= '<button class="mod-del-info delete" name="Elimina" title="Elimina" type="submit" ';
					  			$result .= 'onclick="return confirm(\'Sicuro di voler elliminare la pagina selezionata?\')" ></button>';
					  			$result .= '</form></td>';
					  			if($widget->published)
					  				$result .= '<td><img src="../images/online.svg" title="Pubblicato"></td>';
					  			else
					  				$result .= '<td><img src="../images/offline.svg" title="Non Pubblicato"></td>';
								$result .= '<td>'.$widget->name_widget.'</td>';
								$result .= '<td>'.$widget->date_creation.'</td>';
								$result .= '<td>'.$widget->user_author.'</td></tr>';
							}
							$result .= '<tr><td><form action="nuovo-widget.php" method="post" >';
				  			$result .= '<input type="hidden" name="pos_widget" value="'.($i+1).'"/>';
				  			$result .= '<input type="hidden" name="type_widget" value="html"/>';
				  			$result .= '<button class="mod-del-info add" name="Inserisci" title="Inserisci" type="submit" ></button>';
				  			$result .= '</form></td><td></td><td></td><td></td><td></td></tr></table>';
				  			echo $result;
						}	
					}
					$widgets = getWidgetsLogin("login", $mysqli);
					$result = '<h3>Login Form: </h3>';
					$result .= '<table><thead><tr>';
					$result .= '<th>Azioni</th><th>Stato</th><th>Nome</th><th>Data Creazione</th><th>Autore</th>';
					$result .= '</tr></thead>';
					foreach($widgets as $widget){
						$result .= '<tr><td><form action="modifica-widget.php" method="post" >';
			  			$result .= '<input type="hidden" name="name_widget_sel" value="'.$widget->name_widget.'" />';
			  			$result .= '<input type="hidden" name="type_widget" value="'.$widget->type.'" />';
			  			$result .= '<button class="mod-del-info edit" name="Modifica" title="Modifica" type="submit" ></button>';
			  			$result .= '</form>';
			  			$result .= '<form action="elimina-widget.php" method="post" >';
			  			$result .= '<input type="hidden" name="name_widget_sel" value="'.$widget->name_widget.'" />';
			  			$result .= '<input type="hidden" name="type_widget" value="'.$widget->type.'" />';
			  			$result .= '<button class="mod-del-info delete" name="Elimina" title="Elimina" type="submit" ';
			  			$result .= 'onclick="return confirm(\'Sicuro di voler elliminare la pagina selezionata?\')" ></button>';
			  			$result .= '</form></td>';
			  			if($widget->published)
			  				$result .= '<td><img src="../images/online.svg" title="Pubblicato"></td>';
			  			else
			  				$result .= '<td><img src="../images/offline.svg" title="Non Pubblicato"></td>';
						$result .= '<td>'.$widget->name_widget.'</td>';
						$result .= '<td>'.$widget->date_creation.'</td>';
						$result .= '<td>'.$widget->user_author.'</td></tr>';
					}
					$result .= '<tr><td><form action="nuovo-widget.php" method="post" >';
		  			$result .= '<input type="hidden" name="pos_widget" value="'.($i+1).'"/>';
		  			$result .= '<input type="hidden" name="type_widget" value="login"/>';
		  			$result .= '<button class="mod-del-info add" name="Inserisci" title="Inserisci" type="submit" ></button>';
		  			$result .= '</form></td><td></td><td></td><td></td><td></td></tr></table>';
		  			echo $result;	
				?>
				
			</div>
		</section>

		<!-- Footer -->
			<footer id="footer">
				<div class="inner">
					<div class="flex">
						<div class="copyright">
							&copy; Untitled. Design: <a href="https://templated.co">TEMPLATED</a>. Images: <a href="https://unsplash.com">Unsplash</a>.
						</div>
						<ul class="icons">
							<li><a href="#" class="icon fa-facebook"><span class="label">Facebook</span></a></li>
							<li><a href="#" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
							<li><a href="#" class="icon fa-linkedin"><span class="label">linkedIn</span></a></li>
							<li><a href="#" class="icon fa-pinterest-p"><span class="label">Pinterest</span></a></li>
							<li><a href="#" class="icon fa-vimeo"><span class="label">Vimeo</span></a></li>
						</ul>
					</div>
				</div>
			</footer>

		<!-- Scripts -->
			<script src="../assets/js/jquery.min.js"></script>
			<script src="../assets/js/skel.min.js"></script>
			<script src="../assets/js/util.js"></script>
			<script src="../assets/js/main.js"></script>

	</body>
</html>