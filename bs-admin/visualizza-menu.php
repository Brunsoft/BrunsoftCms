<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/menu/function_menu.php");
	require("../src/pagine/function_page.php");
	require("../src/class/widget.php");
	require("../src/class/page.php");
	sec_session_start();
	
	$name_menu = "";
	$sub_menu = array();
	$id_submenu_sel = 0;
	$name_submenu = "";
	$link_submenu = "";
	$main_submenu = 0;
	$permaname_submenu = "";
	
	unset($_SESSION['message']);
	
	if(isset($_POST['name_menu'])) $name_menu = $_POST['name_menu'];
	elseif(isset($_POST['name_menu_sel'])) $name_menu = $_POST['name_menu_sel'];
	
	if(isset($_POST['id_submenu_sel'])) $id_submenu_sel = $_POST['id_submenu_sel'];
	if(isset($_POST['id_submenu'])) $id_submenu_sel = $_POST['id_submenu'];
	if(isset($_POST['name_submenu'])) $name_submenu = $_POST['name_submenu'];
	if(isset($_POST['link_submenu'])) $link_submenu = $_POST['link_submenu'];
	if(isset($_POST['main_submenu'])) $main_submenu = $_POST['main_submenu'];
	if(isset($_POST['permaname_submenu'])) $permaname_submenu = $_POST['permaname_submenu'];
	
	if(!login_check($mysqli))
		header('Location: ../bs_login.php');
			
	if(isset($_POST['inserisci'])){
		if($main_submenu == 0)
			$main_submenu = null;
		if(checkData()){
			if(searchMenu($name_menu, $mysqli) == 2){
				if($main_submenu == null || searchSubMenu($name_menu, $main_submenu, $mysqli) == 2){
					if(searchPage($link_submenu, $mysqli) == 2){
						$ord_submenu = countSubmenu($name_menu, $main_submenu, $mysqli) + 1;
						$permaname_submenu = findPermaName($name_menu, $name_submenu, $mysqli);
						
						if(insertSubmenu($name_submenu, $name_menu, $main_submenu, $ord_submenu, $link_submenu, $permaname_submenu, $mysqli)){
							$id_submenu_sel = 0;
							$name_submenu = "";
							$link_submenu = "";
							$main_submenu = 0;
							$permaname_submenu = "";
							$_SESSION['message'] = "<nt>Sottovoce inserita con successo</nt>";
						}else
							$_SESSION['message'] = "<er>Errore durante l'inserimento</er>";
					}else
						$_SESSION['message'] = "<er>La pagina selezionata non esiste</er>";	
				}else
					$_SESSION['message'] = "<er>La sottovoce selezionata non esiste</er>";
			}else
				$_SESSION['message'] = "<er>Il menu selezionato non esiste</er>";
		}
	}
	elseif(isset($_POST['modifica'])){
		if($main_submenu == 0)
			$main_submenu = null;
		if(checkData()){
			if(searchMenu($name_menu, $mysqli) == 2){
				if(searchSubMenu($name_menu, $id_submenu_sel, $mysqli) == 2){
					if($main_submenu == null || searchSubMenu($name_menu, $main_submenu, $mysqli) == 2){
						if(searchPage($link_submenu, $mysqli) == 2){
							$sub_menu_sel = getSubMenuSel($name_menu, $id_submenu_sel, $mysqli);
							$ord_submenu = $sub_menu_sel->ord;
							if($sub_menu_sel->main != $main_submenu)
								$ord_submenu = countSubmenu($name_menu, $main_submenu, $mysqli) + 1;
							
							if(updateSubmenu($id_submenu_sel, $name_submenu, $name_menu, $main_submenu, $ord_submenu, $link_submenu, $mysqli)){
								$id_submenu_sel = 0;
								$name_submenu = "";
								$link_submenu = "";
								$main_submenu = 0;
								$permaname_submenu = "";
								$_SESSION['message'] = "<nt>Sottovoce modificata con successo</nt>";
								
							}else
								$_SESSION['message'] = "<er>Errore durante l'update</er>";		
						}else
							$_SESSION['message'] = "<er>La pagina selezionata non esiste</er>";	
					}else
						$_SESSION['message'] = "<er>La sottovoce selezionata non esiste</er>";
				}else
					$_SESSION['message'] = "<er>La sottovoce che si sta cercando di modificare non esiste</er>";	
			}else
				$_SESSION['message'] = "<er>Il menu selezionato non esiste</er>";
		}
	}
	elseif(isset($_POST['elimina']) && isset($_POST['name_menu']) && isset($_POST['id_submenu_sel'])){
		if(searchMenu($_POST['name_menu'], $mysqli) == 2){
			if(searchSubMenu($_POST['name_menu'], $_POST['id_submenu_sel'], $mysqli) == 2){
				if(deleteSubMenuSel($_POST['id_submenu_sel'], $_POST['name_menu'], $mysqli)){
					$id_submenu_sel = 0;
					$_SESSION['message'] = "<nt>Sottovoce eliminata con successo</nt>";
				}else
					$_SESSION['message'] = "<er>Errore durante la cancellazione dei sottomenu</er>";
			}else
				$_SESSION['message'] = "<er>Non esiste alcun submenu con questo id</er>";
		}else
			$_SESSION['message'] = "<er>Non esiste alcun menu con questo nome</er>";
	}	
	elseif(isset($_POST['annulla'])){
		$id_submenu_sel = 0;
		$name_submenu = "";
		$link_submenu = "";
		$main_submenu = 0;
		$permaname_submenu = "";	
	}
	elseif(isset($_POST['indietro'])){
		unset($_SESSION['message']);
		header('Location: gest-menu.php');
	}
	
	if(strcmp($name_menu, "") != 0){
		if(searchMenu($name_menu, $mysqli) == 2){
			$menu = new Widget($name_menu, $mysqli);
			$pos_menu = $menu->pos;
			if(strcmp($pos_menu, "menu") == 0)
				$pos_menu = 1;
			$sub_menu = $menu->sub_menu;
			//echo $sub_menu[0]->sub_menu[0]->id_sub_menu;
			
		}else{
			$_SESSION['message'] = "<er>Menu non trovato</er>";
			header('Location: gest-menu.php');
		}
	}
	
	if(strcmp($id_submenu_sel, 0) != 0 && strcmp($name_menu, "") != 0){
		if(searchSubMenu($name_menu, $id_submenu_sel, $mysqli) == 2){
			$submenu_sel = getSubMenuSel($name_menu, $id_submenu_sel, $mysqli);
			$name_submenu = $submenu_sel->name_sub_menu;
			$link_submenu = $submenu_sel->link;
			$main_submenu = $submenu_sel->main;
			$permaname_submenu = $submenu_sel->permaname;
		}else{
			$_SESSION['message'] = "<er>Submenu non trovato</er>";
			header('Location: gest-menu.php');
		}
	}
	
	function checkData(){
		if(strlen($_POST['name_menu']) > 50 || strlen($_POST['name_menu']) == 0){
			$_SESSION['message'] = "<er>Valore nome menu non corretto</er>";
			return false;
		}elseif(strlen($_POST['name_submenu']) > 50 || strlen($_POST['name_menu']) == 0){
			$_SESSION['message'] = "<er>Valore nome submenu non corretto</er>";
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
		<title>Visualizza Menu</title>
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
					<h2>Visualizza Menu</h2>
					<?php 
					if(isset($_SESSION['message'])) 
						echo $_SESSION['message']; 
					else
						echo "<p>Modifica menu.</p>";	
					?>
				</header>
				
				
				<div class="row uniform">
					<div class="9u 12u$(small)">
						<h3>Struttura menu</h3>
						<?php echo printSubMenu($sub_menu, $id_submenu_sel, 0, $mysqli); ?>
					</div>
					
					<div class="3u$ 12u$(small)">
						<form method="post" action="">
							<input type="hidden" name="name_menu" id="name_menu"  
								value="<?php echo $name_menu; ?>" >
							<h3>
								<?php if($id_submenu_sel != 0) echo 'Modifica sottovoce'; else echo 'Nuova sottovoce'; ?>
							</h3>
							
							<input type="hidden" name="id_submenu" id="id_submenu"  
								value="<?php if($id_submenu_sel != 0) echo $id_submenu_sel; else echo 0;?>">
										
							<div class="12u$">
								Nome Voce	
								<input type="text" name="name_submenu" id="name_submenu" 
									value="<?php echo $name_submenu; ?>" 
									placeholder="Nome*" onKeyDown="nameCounter(this.form.name_submenu);" 
									onKeyUp="nameCounter(this.form.name_submenu);">
							</div>
							<br/>
							<div class="12u$">
								Voce Padre
								<div class="select-wrapper">
									<select name="main_submenu" id="main_submenu" >
										<option value="0" <?php if($main_submenu == 0) echo 'selected=""'; ?>>Voce Principale</option>
										<?php 
											echo printSubMenu1($sub_menu, $main_submenu, 0, $mysqli); ?>
									</select>
								</div>								
							</div>
							<br/>
							<div class="12u$">
								Tipo sottovoce
								<div class="select-wrapper">
									<select name="type_submenu" id="type_submenu" onchange="typeCheck(this.form.type_submenu);" 
										 onload="typeCheck(this.form.type_submenu);">
										<option value="1">Link Pagina</option>
										<option value="2">Link Esterno</option>
										<option value="3">Voce di menu</option>
									</select>
								</div>
							</div>
							
							<div class="12u$" id="div_link_submenu">
								<br/>
								Link Pagina
								<div class="select-wrapper">
									<select name="link_submenu" id="link_submenu" >
										<option value="">Seleziona una sottovoce*</option>
										<?php
											$pages = getPages($mysqli);
											$result = "";
											foreach($pages as $page){
												$result .= '<option value="'.$page->id_page.'"';
												if(strcmp($page->id_page, $link_submenu) == 0)
													$result .= ' selected="" ';
												$result .= '>'.$page->name_page.'</option>';
											}
											echo $result;?>
									</select>
								</div>
							</div>
							
							<div class="12u$" id="div_link_esterno">
								<br/>
								Link Esterno
								<input type="text" name="link_esterno" id="link_esterno" 
									placeholder="Link*" >
							</div>
							
							<hr/>
							<div class="row uniform">
								<div class="12u">
									<?php if(isset($_POST['id_submenu_sel'])){
										echo '<input type="submit" name="annulla" value="Annulla" style="float: left;">';
										echo '<input type="submit" name="modifica" value="Modifica" style="float: right;">';
									}else{
										echo '<input type="submit" name="indietro" value="Indietro" style="float: left;">';
										echo '<input type="submit" name="inserisci" value="Inserisci" style="float: right;">'; 
									}
									?>	
								</div>
							</div>
						</form>
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
			<script>
			function typeCheck(textField) {
			    if (textField.value == 1) {
			        document.getElementById("div_link_submenu").style.display = 'block';
			        document.getElementById("div_link_esterno").style.display = 'none';
				} else if (textField.value == 2) { 
					document.getElementById("div_link_esterno").style.display = 'block';
			        document.getElementById("div_link_submenu").style.display = 'none';
				}else if (textField.value == 3) {
					document.getElementById("div_link_esterno").style.display = 'none';
			        document.getElementById("div_link_submenu").style.display = 'none';
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
			window.onload = typeCheck(document.getElementById("type_submenu"));
			</script>
			<script src="../assets/js/jquery.min.js"></script>
			<script src="../assets/js/skel.min.js"></script>
			<script src="../assets/js/util.js"></script>
			<script src="../assets/js/main.js"></script>

	</body>
</html>