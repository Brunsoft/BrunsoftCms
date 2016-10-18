<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/articoli/function_article.php");
	require("../src/class/article.php");
	sec_session_start();

	unset($_SESSION['message']);
	
	$old_name_article = "";
	$name_article = "";
	$html_article = "";
	$category_article = "";
	$public_article = 0;
	
	if(isset($_POST['old_name_article'])) $old_name_article = $_POST['old_name_article'];
	if(isset($_POST['name_article'])) $name_article = $_POST['name_article'];
	if(isset($_POST['html_article'])) $html_article = $_POST['html_article'];
	if(isset($_POST['category_article'])) $category_article = $_POST['category_article'];
	if(isset($_POST['public_article'])) $public_article = 1;
		
	if(!login_check($mysqli))
		header('Location: ../bs-login.php');
	if(isAdmin($mysqli) != 2)
		header('Location: ../');
	
	elseif(isset($_POST['modifica']) && isset($_POST['old_name_article']) && isset($_POST['name_article']) && isset($_POST['category_article'])){
		if(checkData()){
				
			if(searchArticle($old_name_article, $mysqli) == 2){
				if(searchArticle($name_article, $mysqli) == 1 || $old_name_article == $name_article){
					if(searchCategory($category_article, $mysqli) == 2){
						if(updateArticle($old_name_article, $name_article, $category_article, $html_article, $public_article, $mysqli)){
							resetVar();
							header('Location: gest-articoli.php');	
						}else
							$_SESSION['message'] = "<er>Errore durante l'update</er>";
					}else
						$_SESSION['message'] = "<er>Non esiste una categoria con questo nome</er>";
				}else
					$_SESSION['message'] = "<er>Esiste gia un articolo con questo nome</er>";
			}else
				$_SESSION['message'] = "<er>Non esiste un articolo con questo nome</er>";
		}
	}elseif(isset($_POST['name_article_sel'])){
		if(strlen($_POST['name_article_sel']) > 50 || strlen($_POST['name_article_sel']) == 0){
			$_SESSION['message'] = "<er>Valore nome articolo non corretto</er>";
			header('Location: gest-articoli.php');
		}
		$article = new Article($_POST['name_article_sel'], $mysqli);
		$old_name_article = $article->name_article;
		$name_article = $article->name_article;
		$html_article = $article->html;
		$category_article = $article->category;
		$public_article = $article->published;
	}		
	
	function checkData(){
		if(strlen($_POST['name_article']) > 50 || strlen($_POST['name_article']) == 0){
			$_SESSION['message'] = "<er>Valore nome articolo non corretto</er>";
			return false;
		}elseif(strlen($_POST['html_article']) > 2048){
			$_SESSION['message'] = "<er>Valore contenuto articolo non corretto</er>";
			return false;
		}elseif(strlen($_POST['category_article']) > 50 || strlen($_POST['category_article']) == 0){
			$_SESSION['message'] = "<er>Valore nome categoria non corretto</er>";
			return false;
		}else
			return true;
	}
	
	function resetVar(){
		unset($_POST['old_name_article']);
		unset($_POST['name_article']);
		unset($_POST['html_article']);
		unset($_POST['category_article']);
		if(isset($_POST['public_article']))
			unset($_POST['public_article']);
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
		<title>Modifica Articolo</title>
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
					<h2>Modifica Articolo</h2>
					<?php 
					if(isset($_SESSION['message'])) 
						echo $_SESSION['message']; 
					else
						echo "<p>Modifica articolo selezionato.</p>";	
					?>
				</header>
				<form method="post" action="">
					<div class="row uniform">
						<div class="9u 12u$(small)">
							<input type="hidden" name="old_name_article" id="old_name_article" required="" 
								value="<?php echo $old_name_article; ?>" >
							<strong>Nome Articolo</strong>
							<input type="text" name="name_article" id="name_article" required="" 
								value="<?php echo $name_article; ?>" 
								placeholder="Nome*" onKeyDown="nameCounter(this.form.name_article);" 
								onKeyUp="nameCounter(this.form.name_article);">
							<span id="nameCount">
								<?php echo 50 - strlen($name_article); ?>
							</span> Caratteri rimanenti.
							<br/>
							<strong>Contenuto</strong>
							<textarea name="html_article" id="html_article" placeholder="Html personalizzato" rows="6" 
								onKeyDown="descrCounter(this.form.html_article);" 
								onKeyUp="descrCounter(this.form.html_article);"><?php echo $html_article;?></textarea>
							<span id="descrCount">
								<?php echo 2048 - strlen($html_article);?>
							</span> Caratteri rimanenti.
						</div>
						<div class="3u$ 12u$(small)">
							<strong>Categoria</strong>	
							<div class="12u">
								<?php 
								$categories = getCategories($mysqli);
								echo '<select name="category_article" id="category_article" required="">';
								for($i=0; $i<count($categories); $i++){
									echo '<option value="'.$categories[$i].'" ';
									if(strcmp($category_article, $categories[$i]) == 0)
										echo 'selected=""';
									echo '>'.$categories[$i].'</option>';
								}
								echo '</select>';
								?>									
							</div>			
							<hr/>			
							<div class="12u">
								<input type="checkbox" id="public_article" name="public_article[]" 
								<?php if($public_article == 1) echo 'checked=""'; ?>>
								<label for="public_article">Pubblica</label>
								<br/>
								<div class="row uniform">
									<div class="12u">
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
			var maxDescrSize = 2048;
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