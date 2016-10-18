<?php
class View{
    public $model;
    public $controller;

    public function __construct($controller, $model){
        $this->controller = $controller;
        $this->model = $model;
    }

    public function output(){
    	$logged = false;
    	if(isAdmin($this->model->mysqli) >= 1)
    		$logged = true;
    		
    	$result = '
        <!DOCTYPE HTML>
		<html>
			<head>
				<title>Intensify by TEMPLATED</title>
				<meta charset="utf-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<link rel="stylesheet" href="/mvc/assets/css/main.css" />
			</head>
		';
		
		if($this->model->page->home == 1)
			$result .= '<body>';
		else
			$result .= '<body class="subpage">';
		
		$result .= '
			<header id="header">
				<div class="inner">
					<a href="index.php" class="logo">Brunsoft</a>
						<nav id="nav">'.$this->model->page->toString('menu', $this->model->mysqli, $logged).'</nav>	
					<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
				</div>
			</header>';
			
		$result .= "<!-- Banner -->".$this->model->page->toString('banner', $this->model->mysqli, $logged);
		
		$result .= "<!-- TopA -->".$this->model->page->toString('topA', $this->model->mysqli, $logged);
		
		$result .= "<!-- TopB -->".$this->model->page->toString('topB', $this->model->mysqli, $logged);
		
		$result .= "<!-- Main -->".$this->model->page->toString('main', $this->model->mysqli, $logged);
		
		$result .= "<!-- BottomA -->".$this->model->page->toString('bottomA', $this->model->mysqli, $logged);
		
		$result .= "<!-- BottomB -->".$this->model->page->toString('bottomB', $this->model->mysqli, $logged);
		
		$result .= "<!-- Footer -->".$this->model->page->toString('footer', $this->model->mysqli, $logged);

		$result .= '<!-- Scripts -->
				<script src="/mvc/assets/js/jquery.min.js"></script>
				<script src="/mvc/assets/js/skel.min.js"></script>
				<script src="/mvc/assets/js/util.js"></script>
				<script src="/mvc/assets/js/main.js"></script>
			</body>
		</html>';
		
        return $result;
    }
}
?>