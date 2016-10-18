<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/pagine/function_page.php");
	require("../src/class/page.php");
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
		<title>Gestione Pagine</title>
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
					<h2>Gestione Pagine</h2>
					<?php 
					if(isset($_SESSION['message'])) 
						echo $_SESSION['message']; 
					else
						echo "<p>Crea, modifica elimina pagine.</p>";	
					?>
				</header>
				<?php 
					$pages = getPages($mysqli);
					$result = '<thead><tr><th>Azioni</th><th>Titolo</th><th>Permalink</th><th>Data Creazione</th></tr></thead>';
					foreach($pages as $page){
						$result .= "<tr><td><form action=\"modifica-pagina.php\" method=\"post\" >";
			  			$result .= "<input type=\"hidden\" name =\"id_page_sel\" value=\"".$page->id_page."\" />";
			  			$result .= "<button class=\"mod-del-info edit\" name=\"Modifica\" title=\"Modifica\" type=\"submit\" ></button></form>";
			  			$result .= "<form action=\"elimina-pagina.php\" method=\"post\" >";
			  			$result .= "<input type=\"hidden\" name =\"id_page_sel\" value=\"".$page->id_page."\" />";
			  			$result .= "<button class=\"mod-del-info delete\" name=\"Elimina\" title=\"Elimina\" type=\"submit\" onclick=\"return confirm('Sicuro di voler elliminare la pagina selezionata?')\" ></button></form></td>";
						$result .= "<td>".$page->name_page."</td><td>".$page->permalink.$page->permaname."</td><td>".$page->date_creation."</td></tr>";
					}
					$result .= "<tr><td><form action=\"nuova-pagina.php\" method=\"post\" >";
		  			$result .= "<input type=\"hidden\" name =\"name_page\" />";
		  			$result .= "<button class=\"mod-del-info add\" name=\"Inserisci\" title=\"Inserisci\" type=\"submit\" ></button></form>";
		  			$result .= "</td><td></td><td></td><td></td></tr>";
				?>
				<table>
					<?php echo $result; ?>
				</table>
				
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