<?php
	require("../src/database/db_connect.php");
	require("../src/login/function_login.php");
	require("../src/widget/function_widget.php");
	require("../src/class/widget.php");
	require("../src/pagine/function_page.php");
	require("../src/class/page.php");
	sec_session_start();

	unset($_SESSION['message']);
		
	$old_name_widget = "";
	$name_widget = "";
	$html_widget = "";	
	$page_widget = array();
	$pos_widget = 0;
	$dim_widget = 0;
	$public_widget = 0;
	$type_widget = "";
	
	if(isset($_POST['old_name_widget'])) $old_name_widget = $_POST['old_name_widget'];
	if(isset($_POST['name_widget'])) $name_widget = $_POST['name_widget'];
	if(isset($_POST['html_widget'])) $html_widget = $_POST['html_widget'];
	if(isset($_POST['page_widget'])) $page_widget = $_POST['page_widget'];
	if(isset($_POST['pos_widget'])) $pos_widget = $_POST['pos_widget'];
	if(isset($_POST['dim_widget'])) $dim_widget = $_POST['dim_widget'];
	if(isset($_POST['public_widget'])) $public_widget = $_POST['public_widget'];
	if(isset($_POST['type_widget'])) $type_widget = $_POST['type_widget'];
		
	if(!login_check($mysqli))
		header('Location: ../bs-login.php');
	if(isAdmin($mysqli) != 2)
		header('Location: ../');
	
	elseif(isset($_POST['modifica']) && isset($_POST['old_name_widget']) && isset($_POST['name_widget']) && isset($_POST['pos_widget']) && isset($_POST['dim_widget'])){
		if(checkData()){
			$pos_widget = (int)$_POST['pos_widget'];
			$dim_widget = (int)$_POST['dim_widget'];
				
			$wrong_pages = 0;
			
			if(isset($_POST['page_widget']))
				for($i=0; $i<count($page_widget); $i++)
					if(searchPage($page_widget[$i], $mysqli) == 1)
						$wrong_pages++;
			
			if($wrong_pages	== 0){
				if(searchWidget($name_widget, $mysqli) == 1 || strcmp($name_widget, $old_name_widget) == 0){
					if(searchWidget($old_name_widget, $mysqli) == 2){
						if(updateWidget($old_name_widget, $name_widget, $html_widget, 0, $pos_widget, $dim_widget, $type_widget, $public_widget, $mysqli)){
							deleteWidgetPage($name_widget, $mysqli);
							$wrong_insert = 0;
							for($i=0; $i<count($page_widget); $i++)
								if(!insertWidgetPage($page_widget[$i], $name_widget, $mysqli))
									$wrong_insert++;
							if($wrong_insert == 0){
								resetVar();
								header('Location: gest-widget.php');	
							}else
								$_SESSION['message'] = "<er>Errore durante l'associazine pagina-widget</er>";
						}else
							$_SESSION['message'] = "<er>Errore durante l'inserimento</er>";
					}else
						$_SESSION['message'] = "<er>Non esiste un widget con questo nome</er>";
				}else
					$_SESSION['message'] = "<er>Esiste gia un widget con questo nome</er>";
			}else
				$_SESSION['message'] = "<er>Una o piu pagine selezionate non esistono</er>";
		}
	}elseif(isset($_POST['name_widget_sel'])){
		if(strlen($_POST['name_widget_sel']) > 50 || strlen($_POST['name_widget_sel']) == 0){
			$_SESSION['message'] = "<er>Valore nome widget non corretto</er>";
			header('Location: gest-widget.php');
		}
		$widget = new Widget($_POST['name_widget_sel'], $mysqli);
		$old_name_widget = $widget->name_widget;
		$name_widget = $widget->name_widget;
		$html_widget = $widget->html;
		$page_widget = getWidgetPages($name_widget, $mysqli);
		switch($widget->pos){
			case 'banner':
				$pos_widget = 1;
				break;
			case 'topA':
				$pos_widget = 2;
				break;
			case 'topB':
				$pos_widget = 3;
				break;
			case 'bottomA':
				$pos_widget = 4;
				break;
			case 'bottomB':
				$pos_widget = 5;
				break;
			case 'footer':
				$pos_widget = 6;
				break;
			default:
				$_SESSION['message'] = "<er>Errore durante l'associazione della posizione</er>";
				break;
		}
		$dim_widget = $widget->dim;
		$public_widget = $widget->published;
	}	
	
	function checkData(){
		$type = (int)$_POST['pos_widget'];
		$dim = (int)$_POST['dim_widget'];
		if(strlen($_POST['name_widget']) > 50 || strlen($_POST['name_widget']) == 0){
			$_SESSION['message'] = "<er>Valore nome widget non corretto</er>";
			return false;
		}elseif(strlen($_POST['html_widget']) > 2048){
			$_SESSION['message'] = "<er>Valore contenuto widget non corretto</er>";
			return false;
		}elseif($type < 1 || $type > 6){
			$_SESSION['message'] = "<er>Valore posizione widget non corretto</er>";
			return false;
		}elseif($dim < 1 || $dim > 4){
			$_SESSION['message'] = "<er>Valore dimensione widget non corretto</er>";
			return false;
		}else
			return true;
	}
	
	function resetVar(){
		unset($_POST['name_widget']);
		unset($_POST['html_widget']);
		unset($_POST['pos_widget']);
		if(isset($_POST['public_widget']))
			unset($_POST['public_widget']);
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
		<title>Modidica Widget</title>
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
					<h2>Modifica Widget</h2>
					<?php 
					if(isset($_SESSION['message'])) 
						echo $_SESSION['message']; 
					else
						echo "<p>Modifica widget.</p>";	
					?>
				</header>
				<form method="post" action="">
					<div class="row uniform">
						<div class="9u 12u$(small)">
							<input type="hidden" name="old_name_widget" id="old_name_widget" required="" 
								value="<?php echo $old_name_widget; ?>" >
							<input type="hidden" name="type_widget" id="type_widget" required="" 
								value="<?php echo $type_widget; ?>" >
							<strong>Nome widget</strong>
							<input type="text" name="name_widget" id="name_widget" required="" 
								value="<?php echo $name_widget; ?>" 
								placeholder="Nome*" onKeyDown="nameCounter(this.form.name_widget);" 
								onKeyUp="nameCounter(this.form.name_widget);">
							<span id="nameCount">
								<?php echo 50 - strlen($name_widget); ?>													
							</span> Caratteri rimanenti.
							<br/>		
							<strong>Contenuto</strong>
							<textarea name="html_widget" id="html_widget" placeholder="Html personalizzato" rows="6" 
								onKeyDown="descrCounter(this.form.html_widget);" 
								onKeyUp="descrCounter(this.form.html_widget);"><?php echo $html_widget;?></textarea>
							<span id="descrCount">
								<?php echo 2048 - strlen($html_widget);?>
							</span> Caratteri rimanenti.
							
							<br/><br/>
							<strong>Associa alle pagine</strong>	
							<div class="12u$">
								<?php 
									$pages = getPages($mysqli);
									foreach($pages as $page){
										echo '<input type="checkbox" id="page_widget:check:'.$page->id_page;
										if(in_array($page->id_page, $page_widget))
											echo '" name="page_widget[]" value="'.$page->id_page.'" checked="">';
										else
											echo '" name="page_widget[]" value="'.$page->id_page.'" >';
										echo '<label for="page_widget:check:'.$page->id_page.'">'.$page->name_page.'</label>';
										}
								?>									
							</div>
						</div>
						
						<div class="3u$ 12u$(small)">
							<strong>Posizione</strong>
							<div class="select-wrapper">
								<select name="pos_widget" id="pos_widget" required="">
									<option value="">Posizione widget*</option>
									<option value="1" <?php if($pos_widget == 1) echo 'selected=""'; ?> >Banner</option>
									<option value="2" <?php if($pos_widget == 2) echo 'selected=""'; ?> >Top A</option>
									<option value="3" <?php if($pos_widget == 3) echo 'selected=""'; ?> >Top B</option>
									<option value="4" <?php if($pos_widget == 4) echo 'selected=""'; ?> >Bottom A</option>
									<option value="5" <?php if($pos_widget == 5) echo 'selected=""'; ?> >Bottom B</option>
									<option value="6" <?php if($pos_widget == 6) echo 'selected=""'; ?> >Footer</option>
								</select>
							</div>
							<br/>
							<strong>Dimensione</strong>
							<div class="select-wrapper">
								<select name="dim_widget" id="dim_widget" required="">
									<option value="">Dimensione widget*</option>
									<option value="1" <?php if($dim_widget == 1) echo 'selected=""'; ?>	>100%</option>
									<option value="2" <?php if($dim_widget == 2) echo 'selected=""'; ?>	>50%</option>
									<option value="3" <?php if($dim_widget == 3) echo 'selected=""'; ?>	>35%</option>
									<option value="4" <?php if($dim_widget == 4) echo 'selected=""'; ?>	>25%</option>
								</select>
							</div>
							<hr/>
							<div class="12u$">
								<input type="checkbox" id="public_widget" name="public_widget[]" 
									<?php if($public_widget == 1) echo 'checked=""'; ?>>
								<label for="public_widget">Pubblica</label>
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