<?php
	require("src/database/db_connect.php");
	require("src/login/function_login.php");
	sec_session_start();
	
	if(login_check($mysqli))
		header('Location: bs-admin/index.php');
	elseif(isset($_POST['email']) && isset($_POST['pwd']) && isset($_POST['p'])){
		if(login($_POST['email'], $_POST['p'], $mysqli))
			header('Location: bs-admin/index.php');
		else
			header('Location: bs-login.php');
	}
?>

<!DOCTYPE HTML>
<!--
	Theory by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
<html>
	<head>
		<title>Pagina di Login</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<script src="js/sha512.js"></script>
	</head>
	<body class="subpage">

		<!-- Header -->
		<header id="header">
			<div class="inner">
				<a href="bs_login.php" class="logo">Brunsoft</a>
				<nav id="nav">
					<a href="#">Pagine</a>
					<a href="#">Menu</a>
					<a href="#">Articoli</a>
					<a href="#">Widget</a>
				</nav>
				<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
			</div>
		</header>
			
		<!-- Main -->
		<section id="main" class="wrapper">
			<header class="align-center">
				<h2>Area riservata</h2>
				<?php if(isset($_SESSION['message'])) echo "<p>".$_SESSION['message']."</p>" ?>
			</header>
			<div>
				<form method="post" name="login_form" id="login_form" class="align-center">
					Email: <input type="text" name="email" id="email" style="max-width: 300px; margin: 0 auto;"/><br />
					Password: <input type="password" name="pwd" id="pwd" style="max-width: 300px; margin: 0 auto;"/><br />
				   	<input type="button" value="Login" id="button_login"/>
				</form>
			</div>
		</section>
		
		<!-- Footer -->
		<footer id="footer">
			<div class="inner">
				<div class="flex">
					<div class="copyright">
						&copy; Brunsoft. Design: <a href="https://templated.co">TEMPLATED</a>. Images: <a href="https://unsplash.com">Unsplash</a>.
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
		<script>
			document.getElementById("button_login").addEventListener("click", function(){
				var p = document.createElement("input");
				var form = document.getElementById("login_form");
				var pwd = document.getElementById("pwd");
			   	form.appendChild(p);
			   	p.name = "p";
			   	p.type = "hidden"
			   	p.value = hex_sha512(pwd.value);
			   	pwd.value = "";
			   	form.submit();
			});
			
		</script>
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>