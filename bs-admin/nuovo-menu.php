<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/menu/function_menu.php");
	require("../src/pagine/function_page.php");
	require("../src/class/widget.php");
	require("../src/class/page.php");
	sec_session_start();

	if(!login_check($mysqli))
		header('Location: ../bs_login.php');
			
	elseif(isset($_POST['inserisci']) && isset($_POST['name_menu']) && isset($_POST['pos_menu'])){
		
		if(checkData()){
			$pos = (int)$_POST['pos_menu'];
				
			$public = 0;
			if(isset($_POST['public_menu']))
				$public = 1;
					
			if($pos == 1)
				$pos = "menu";
				
			if(searchMenu($_POST['name_menu'], $mysqli) == 1){
				if(insertMenu($_POST['name_menu'], $pos, "menu", $public, $mysqli)){
					// inserisci associazione pagine-menu
					if(isset($_POST['page_menu'])){
						$pagine_sel = $_POST['page_menu'];
						$n_pagine_sel = count($pagine_sel);
			    		for($i=0; $i < $n_pagine_sel; $i++){
			    			insertPageMenu($pagine_sel[$i], $_POST['name_menu'], $mysqli);
			    		}
					}
					resetVar();
					unset($_SESSION['message']);
					header('Location: gest-menu.php');	
				}else
					$_SESSION['message'] = "<er>Errore durante l'inserimento</er>";
			}else
				$_SESSION['message'] = "<er>Esiste gia un menu con questo nome</er>";
			
				
		}
	}else
		unset($_SESSION['message']);
	
	function checkData(){
		$pos = (int)$_POST['pos_menu'];
		if(strlen($_POST['name_menu']) > 50 || strlen($_POST['name_menu']) == 0){
			$_SESSION['message'] = "<er>Valore nome menu non corretto</er>";
			return false;
		}elseif($pos != 1){
			$_SESSION['message'] = "<er>Valore posizione menu non corretto</er>";
			return false;
		}else
			return true;
	}
	
	function resetVar(){
		unset($_POST['name_menu']);
		unset($_POST['pos_menu']);
		if(isset($_POST['page_menu']))
			unset($_POST['page_menu']);
		if(isset($_POST['public_menu']))
			unset($_POST['public_menu']);
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
		<title>Nuovo Menu</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="../assets/css/main.css" />
	</head>
	<body class="subpage">

		<!-- Header -->
		<header id="header">
			<div class="inner">
				<a href="bs_login.php" class="logo">Brunsoft</a>
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
					<h2>Nuovo Menu</h2>
					<?php 
					if(isset($_SESSION['message'])) 
						echo $_SESSION['message']; 
					else
						echo "<p>Crea un nuovo menu.</p>";	
					?>
				</header>
				<form method="post" action="">
					<div class="row uniform">
						<div class="9u 12u$(small)">
							<h4>Nome menu</h4>
							<input type="text" name="name_menu" id="name_menu" required="" 
								value="<?php if(isset($_POST['name_menu'])) echo $_POST['name_menu']; ?>" 
								placeholder="Nome*" onKeyDown="nameCounter(this.form.name_menu);" 
								onKeyUp="nameCounter(this.form.name_menu);">
							<span id="nameCount">
								<?php if(isset($_POST['name_menu'])) echo 50 - strlen($_POST['name_menu']);
								else echo "50"; ?>																		
							</span> Caratteri rimanenti.
							<hr/>
							<h4>Assegnazione alle pagine:</h4>
							<div class="12u$">
							
								<div class="12u$">
									<?php 
										$name_menu = "";
										if(isset($_POST['name_menu'])) $name_menu = $_POST['name_menu'];
										$page_menu = array();
										if(isset($_POST['page_menu'])) $page_menu = $_POST['page_menu'];
										$pages = getNoMenuPages($name_menu, $page_menu, $mysqli);
										foreach($pages as $page){
					echo "<input type=\"checkbox\" id=\"page_menu:check:".$page->name_page."\" name=\"page_menu[]\" value=\"".$page->name_page."\">";
					echo "<label for=\"page_menu:check:".$page->name_page."\">".$page->name_page."</label>";
										}
										if(count($pages) == 0)
											echo "<p>Nessuna pagina senza menu</p>"
									?>									
								</div>
							</div>
						</div>
						
						<div class="3u$ 12u$(small)">
							<h4>Pubblica Menu</h4>
							<div class="select-wrapper">
								<select name="pos_menu" id="pos_menu" required="">
									<option value="">Posizione Menu*</option>
									<option value="1" 
										<?php if(isset($_POST['pos_menu']) && $_POST['pos_menu'] == 1) 
											echo "selected=\"\""; ?> 
										>Main Menu</option>
								</select>
							</div>
							<br/>
							<div class="12u$">
								<input type="checkbox" id="public_menu" name="public_menu[]" 
								<?php if(isset($_POST['public_menu'])) echo "checked=\"\""; ?>>
								<label for="public_menu">Pubblica</label>
								
								<div class="row uniform">
									<div class="12u">
										<input type="submit" name="inserisci" value="Inserisci" style="float: right;">
									</div>
								</div>
							</div>
						</div>
						
					</div>
				</form>

				
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
			<script>
			var maxDescrSize = 156;
			function descrCounter(textField) {
			    if (textField.value.length > maxDescrSize) {
			        textField.value = textField.value.substring(0, maxDescrSize);
				} else { 
			        document.getElementById("descrCount").innerHTML = maxDescrSize - textField.value.length;
				}
			}
			var maxTitleSize = 65;
			function titleCounter(textField) {
			    if (textField.value.length > maxTitleSize) {
			        textField.value = textField.value.substring(0, maxTitleSize);
				} else { 
			        document.getElementById("titleCount").innerHTML = maxTitleSize - textField.value.length;
				}
			}
			var maxNameSize = 50;
			function nameCounter(textField) {
			    if (textField.value.length > maxNameSize) {
			        textField.value = textField.value.substring(0, maxNameSize);
				} else { 
			        document.getElementById("nameCount").innerHTML = maxNameSize - textField.value.length;
				}
			}
			</script>
			<script src="../assets/js/jquery.min.js"></script>
			<script src="../assets/js/skel.min.js"></script>
			<script src="../assets/js/util.js"></script>
			<script src="../assets/js/main.js"></script>

	</body>
</html>