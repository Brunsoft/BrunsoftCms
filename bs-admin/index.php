<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/pagine/function_page.php");
	require("../src/articoli/function_article.php");
	require("../src/widget/function_widget.php");
	sec_session_start();
	
	if(!login_check($mysqli))
		header('Location: ../bs_login.php');
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
		<title>Pagina di Amministrazione</title>
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
					<h2>Pagina di Amministrazione</h2>
					<p>Aliquam erat volutpat nam dui </p>
				</header>
				<div class="flex flex-4 align-center">
					<div class="box">
						<h3><a href="gest-pagine.php">Pagine</a></h3>
						<div><img src="../images/online.svg"><?php echo ' '.getOnlinePages($mysqli).' '; ?>Pagine online</div>
						<div><img src="../images/offline.svg"><?php echo ' '.getOfflinePages($mysqli).' '; ?>Pagine offline</div>
					</div>
					<div class="box">
						<h3><a href="gest-menu.php">Menu</a></h3>
						<div><img src="../images/online.svg"><?php echo ' '.getOnlineMenus($mysqli).' '; ?>Menu online</div>
						<div><img src="../images/offline.svg"><?php echo ' '.getOfflineMenus($mysqli).' '; ?>Menu offline</div>
					</div>
					<div class="box">
						<h3><a href="gest-articoli.php">Articoli</a></h3>
						<div><img src="../images/online.svg"><?php echo ' '.getOnlineArticles($mysqli).' '; ?>Articoli online</div>
						<div><img src="../images/offline.svg"><?php echo ' '.getOfflineArticles($mysqli).' '; ?>Articoli offline</div>
					</div>
					<div class="box">
						<h3><a href="gest-widget.php">Widget</a></h3>
						<div><img src="../images/online.svg"><?php echo ' '.getOnlineWidgets($mysqli).' '; ?>Widget online</div>
						<div><img src="../images/offline.svg"><?php echo ' '.getOfflineWidgets($mysqli).' '; ?>Widget offline</div>
					</div>
				</div>
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