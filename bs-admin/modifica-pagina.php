<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/pagine/function_page.php");
	require("../src/class/page.php");
	require("../src/articoli/function_article.php");
	sec_session_start();

	$id_page = "";
	$name_page = "";
	$title_page = "";
	$descr_page = "";
	$type_page = "";
	$permaname_page = "";
	$main_page = null;
	$public_page = "";
	$permalink_page = "";
	$category_article = null;
	$name_article = "";
	
	if(isset($_POST['id_page'])) $id_page = $_POST['id_page'];
	if(isset($_POST['name_page'])) $name_page = $_POST['name_page'];
	if(isset($_POST['title_page'])) $title_page = $_POST['title_page'];
	if(isset($_POST['descr_page'])) $descr_page = $_POST['descr_page'];
	if(isset($_POST['type_page'])) $type_page = $_POST['type_page'];
	if(isset($_POST['permaname_page'])) $permaname_page = $_POST['permaname_page'];
	if(isset($_POST['main_page']) && $_POST['main_page'] != 0) $main_page = $_POST['main_page'];
	if(isset($_POST['public_page'])) $public_page = $_POST['public_page'];
	if(isset($_POST['permalink_page'])) $permalink_page = $_POST['permalink_page'];
	if(isset($_POST['category_article'])) $category_article = $_POST['category_article'];
	if(isset($_POST['name_article'])) $name_article = $_POST['name_article'];
	

	if(!login_check($mysqli))
		header('Location: ../bs-login.php');
	if(isAdmin($mysqli) != 2)
		header('Location: ../');
		
	elseif(isset($_POST['annulla'])){
		unset($_SESSION['message']);
		header('Location: gest-pagine.php');
	}
		
	elseif(isset($_POST['modifica']) && isset($_POST['id_page']) && isset($_POST['name_page']) && isset($_POST['title_page']) && isset($_POST['descr_page']) && isset($_POST['type_page']) && isset($_POST['permaname_page'])){
		if(checkData()){
			$type = (int)$_POST['type_page'];
			$public = 0;
			if(isset($_POST['public_page'])) $public = 1;
			if($type_page != 2)	$category_article = null;
			if(searchPage($id_page, $mysqli) == 2){
				if(searchPageName($name_page, $id_page, $mysqli) == 1){
					if(updatePage($id_page, $name_page, $title_page, $descr_page, $type, $public, $main_page, $permaname_page, $category_article, $mysqli)){	
						if(deleteArticlePage($id_page, $mysqli)){
							if($type_page == 1)
								insertArticlePage($id_page, $name_article, $mysqli);
							
							resetVar();
							unset($_SESSION['message']);
							header('Location: gest-pagine.php');
						}else
							$_SESSION['message'] = "<er>Errore durante la cancelazione delle vecchie pagine</er>";
					}else
						$_SESSION['message'] = "<er>Errore durante l'update</er>";
				}else
					$_SESSION['message'] = "<er>Esiste gia una pagina con questo nome</er>";
			}else
				$_SESSION['message'] = "<er>Non esiste alcuna pagina con questo nome</er>";
		}
	}elseif(isset($_POST['id_page_sel'])){
		if(searchPage($_POST['id_page_sel'], $mysqli) == 2){
			$page = new Page($_POST['id_page_sel'], $mysqli);
			$id_page = $page->id_page;
			$name_page = $page->name_page;
			$title_page = $page->title;
			$descr_page = $page->descr;
			$type_page = $page->type;
			$permaname_page = $page->permaname;
			$main_page = $page->main_page;
			$public_page = $page->published;
			$permalink_page = $page->permalink;
			if($type_page == 1)
				$name_article = getNameArticlePageSel($id_page, $mysqli);
			elseif($type_page == 2)
				$category_article = $page->category_articles;
				
		}else{
			$_SESSION['message'] = "<er>Pagina non trovata</er>";
			header('Location: gest-pagine.php');
		}
	}else{
		unset($_SESSION['message']);
		header('Location: gest-pagine.php');
	}
	
	function checkData(){
		$type = (int)$_POST['type_page'];
		if(strlen($_POST['name_page']) > 50 || strlen($_POST['name_page']) == 0){
			$_SESSION['message'] = "<er>Valore nome pagina non corretto</er>";
			return false;
		}elseif(strlen($_POST['title_page']) > 65 || strlen($_POST['title_page']) == 0){
			$_SESSION['message'] = "<er>Valore titolo pagina non corretto</er>";
			return false;
		}elseif(strlen($_POST['descr_page']) > 156 || strlen($_POST['descr_page']) == 0){
			$_SESSION['message'] = "<er>Valore descrizione pagina non corretto</er>";
			return false;
		}elseif($type < 1 || $type > 3){
			$_SESSION['message'] = "<er>Valore tipo pagina non corretto</er>";
			return false;
		}else
			return true;
	}
	
	function resetVar(){
		unset($_POST['name_page']);
		unset($_POST['title_page']);
		unset($_POST['descr_page']);
		unset($_POST['type_page']);
		if(isset($_POST['public_page']))
			unset($_POST['public_page']);
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
		<title>Modifica Pagina</title>
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
					<h2>Modifica Pagina</h2>
					<?php 
					if(isset($_SESSION['message'])) 
						echo $_SESSION['message']; 
					else
						echo "<p>Modifica la pagina selezionata.</p>";	
					?>
				</header>
				<form method="post" action="">
					<div class="row uniform">
						<div class="9u 12u$(small)">
							
							<input type="hidden" name="id_page" id="id_page"  
								value="<?php echo $id_page; ?>" >
								
							<strong>Nome Visualizzato</strong>
							<input type="text" name="name_page" id="name_page" required="" 
								value="<?php echo $name_page; ?>" 
								placeholder="Nome*" onKeyDown="nameCounter(this.form.name_page);" 
								onKeyUp="nameCounter(this.form.name_page);">
							<span id="nameCount">
								<?php echo 50 - strlen($name_page); ?>													
							</span> Caratteri rimanenti.
							
							<br/>
							<strong>Titolo</strong>
							<input type="text" name="title_page" id="title_page" required="" 
								value="<?php echo $title_page; ?>" 
								placeholder="Titolo*" onKeyDown="titleCounter(this.form.title_page);" 
								onKeyUp="titleCounter(this.form.title_page);">
							<span id="titleCount">
								<?php echo 65 - strlen($title_page); ?>
							</span> Caratteri rimanenti.
							
							<br/>
							<strong>Descrizione</strong>
							<textarea name="descr_page" id="descr_page" placeholder="Descrizione*" rows="4" required="" 
								onKeyDown="descrCounter(this.form.descr_page);" 
								onKeyUp="descrCounter(this.form.descr_page);"><?php echo $descr_page; ?></textarea>
							<span id="descrCount">
									<?php echo 156 - strlen($descr_page); ?>
							</span> Caratteri rimanenti.

							<br/>
							<div class="row uniform">
								<div class="9u 12u$(small)">
									<strong>Permalink</strong>
									<input type="text" name="permalink_page" id="permalink_page" readonly="">
								</div>
								<div class="3u 12u$(small)">
									<strong>Permaname</strong>
									<input type="text" name="permaname_page" id="permaname_page"  
										value="<?php echo $permaname_page; ?>" 
										onKeyDown="linkCounter(this.form.permaname_page);" 
										onKeyUp="linkCounter(this.form.permaname_page);">
								</div>
							</div>
						</div>
						
						<div class="3u$ 12u$(small)">
							<strong>Pagina padre</strong>
							<div class="select-wrapper">
								<select name="main_page" id="main_page" required="" >
									<option value="0" <?php if($main_page == null) echo 'selected=""'; ?>>Pagina Principale</option>
									<?php echo getUsablePage($id_page, $main_page, "", 0, $mysqli); ?>
								</select>
							</div>
						
							<br/>
							<strong>Tipo</strong>
							<div class="select-wrapper">
								<select name="type_page" id="type_page" required="" 
									onchange="typeCheck(this.form.type_page);" 
									onload="typeCheck(this.form.type_page);">
									<option value="1" <?php if($type_page == 1) echo 'selected=""'; ?>>Singolo Articolo</option>
									<option value="2" <?php if($type_page == 2) echo 'selected=""'; ?>>Blog</option>
									<option value="3" <?php if($type_page == 3) echo 'selected=""'; ?>>Hidden Article</option>
								</select>
							</div>
							<br/>
							<div class="12u$" id="div_articoli">
								<strong>Articolo</strong>
								<div class="select-wrapper">
									<select name="name_article" id="link_submenu" >
										<?php
											$articles = getArticles($mysqli);
											$result = "";
											foreach($articles as $article){
												$result .= '<option value="'.$article->name_article.'"';
												if(strcmp($article->name_article, $name_article) == 0)
													$result .= ' selected="" ';
												$result .= '>'.$article->name_article.'</option>';
											}
											echo $result;?>
									</select>
								</div>
							</div>
							
							<div class="12u$" id="div_categorie">
								<strong>Categoria</strong>
								<div class="select-wrapper">
									<select name="category_article" id="category_article" >
										<?php
											$categories = getCategories($mysqli);
											$result = "";
											for($i=0; $i<count($categories); $i++){
												$result .= '<option value="'.$categories[$i].'"';
												if(strcmp($categories[$i], $category_article) == 0)
													$result .= ' selected="" ';
												$result .= '>'.$categories[$i].'</option>';
											}
											echo $result;?>
									</select>
								</div>
							</div>
							
							<div class="12u">
								<hr/>
								<input type="checkbox" id="public_page" name="public_page[]" 
								<?php if($public_page == 1) echo "checked=\"\""; ?>>
								<label for="public_page">Pubblica</label>
								<div class="row uniform">
									<div class="6u">
										<input type="submit" name="annulla" value="Annulla" style="float: left;">
									</div>
									<div class="6u">
										<input type="submit" name="modifica" value="Modifica" style="float: right;">
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
			function typeCheck(textField) {
			    if (textField.value == 1) {
			        document.getElementById("div_articoli").style.display = 'block';
			        document.getElementById("div_categorie").style.display = 'none';
				} else if (textField.value == 2) { 
					document.getElementById("div_categorie").style.display = 'block';
			        document.getElementById("div_articoli").style.display = 'none';
				}else if (textField.value == 3) {
					document.getElementById("div_articoli").style.display = 'none';
			        document.getElementById("div_categorie").style.display = 'none';
				}
			}
			var maxDescrSize = 156;
			function descrCounter(textField) {
			    if (textField.value.length > maxDescrSize) {
			        textField.value = textField.value.substring(0, maxDescrSize);
				} else { 
			        document.getElementById("descrCount").innerHTML = maxDescrSize - textField.value.length;
				}
			}
			var maxLinkSize = 50;
			function linkCounter(textField) {
			    if (textField.value.length > maxDescrSize) {
			        textField.value = textField.value.substring(0, maxDescrSize);
				} else { 
					var permalink = "<?php echo $permalink_page; ?>";
					document.getElementById("permalink_page").value =  permalink+textField.value;
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
			window.onload = typeCheck(document.getElementById("type_page"));
			window.onload = linkCounter(document.getElementById("permaname_page"));
			</script>
			<script src="../assets/js/jquery.min.js"></script>
			<script src="../assets/js/skel.min.js"></script>
			<script src="../assets/js/util.js"></script>
			<script src="../assets/js/main.js"></script>

	</body>
</html>