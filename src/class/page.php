<?php
require_once('article.php');
require_once('widget.php');

class Page{
    public $name_page;
    public $descr;
    public $title;
    public $type;		// Single Article, Blog
    public $permaname;
    public $date_creation;
    public $date_last_edit;
    public $user_author;
    public $user_last_edit;
    public $published;
    public $menu;		// Array oggetti Widget type menu
    public $logo;		// Array oggetti Widget type logo
    public $banner;		// Array oggetti Widget type banner
    public $top_a;		// Array oggetti Widget type topA
    public $top_b;		// Array oggetty Widget type topB
    public $main;		// Array oggetti Article
    public $bottom_a;	// Array oggetti Widget type bottomA
    public $bottom_b;	// Array oggetti Widget type bottomB
    public $footer;		// Array oggetti Widget type footer

       
    public function __construct($name_page, $mysqli){
		$this->init($name_page, $mysqli);
    }
    
    public function init($name_page, $mysqli){
    	if ($stmt = $mysqli->prepare("SELECT title, descr, type, permaname, date_creation, date_last_edit, user_author, user_last_edit, published FROM page WHERE name_page = ?")) {
			$stmt->bind_param('s', $name_page);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($title, $descr, $type, $permaname, $date_creation, $date_last_edit, $user_author, $user_last_edit, $published);
			$stmt->fetch(); 
			if ($stmt->affected_rows > 0){
				$this->name_page = $name_page;
				$this->title = $title;
				$this->descr = $descr;
				$this->type = $type;
				$this->permaname = $permaname;
				$this->date_creation = $date_creation;
				$this->date_last_edit = $date_last_edit;
				$this->user_author = $user_author;
				$this->user_last_edit = $user_last_edit;
				$this->published = $published;
			}
			$this->findContent($this->name_page, $mysqli);
		}
	}

	private function findContent($name_page, $mysqli){
    	if ($stmt = $mysqli->prepare("SELECT w.name_widget, w.type FROM widget_page wp JOIN widget w ON wp.name_widget = w.name_widget WHERE w.name_page = ? ORDER BY w.ord")) {
			$stmt->bind_param('s', $name_page);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_widget, $type);
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					if(strcmp($type, "banner") == 0)
						array_push($this->banner, new Widget($name_widget, $mysqli));
					elseif(strcmp($type, "topA") == 0)
						array_push($this->top_a, new Widget($name_widget, $mysqli));
					elseif(strcmp($type, "topB") == 0)
						array_push($this->top_b, new Widget($name_widget, $mysqli));
					elseif(strcmp($type, "bottomA") == 0)
						array_push($this->bottom_a, new Widget($name_widget, $mysqli));
					elseif(strcmp($type, "bottomB") == 0)
						array_push($this->bottom_b, new Widget($name_widget, $mysqli));
					elseif(strcmp($type, "main") == 0)
						array_push($this->main, new Article($name_widget, $mysqli));
					elseif(strcmp($type, "footer") == 0)
						array_push($this->footer, new Widget($name_widget, $mysqli));
					elseif(strcmp($type, "menu") == 0)
						array_push($this->menu, new Widget($name_widget, $mysqli));
					elseif(strcmp($type, "logo") == 0)
						array_push($this->logo, new Widget($name_widget, $mysqli));
					/*foreach($this->banner as $content){
						echo $content->name_content;
					}*/
				}
			}
		}
	}
	
	/*public function toString($content){
		$result = "";
		$contents = array();
		$class = "";
		if(strcmp($content, "banner") == 0){
			$contents = $this->banner;
			$class = $this->class_banner;
		}elseif(strcmp($content, "topA") == 0){
			$contents = $this->top_a;
			$class = $this->class_top_a;
		}elseif(strcmp($content, "topB") == 0){
			$contents = $this->top_b;
			$class = $this->class_top_b;
		}elseif(strcmp($content, "main") == 0){
			$contents = $this->main;
			$class = $this->class_main;	
		}elseif(strcmp($content, "bottomA") == 0){
			$contents = $this->bottom_a;
			$class = $this->class_bottom_a;	
		}elseif(strcmp($content, "bottomB") == 0){
			$contents = $this->bottom_b;
			$class = $this->class_bottom_b;	
		}elseif(strcmp($content, "footer") == 0){
			$contents = $this->footer;
			$class = $this->class_footer;	
		}
		
		if(count($contents) > 0){
			if(strcmp($content, "footer") == 0)
				$result .= '<footer id="'.$content.'" class="'.$class.'">';
			else
				$result .= '<section id="'.$content.'" class="'.$class.'">';
				
			$result .= '<div class="row">';
			
			foreach($contents as $c){
				if($c->dimension == 1)
					$result .= '<div class="12u 12u$(small)">';
				elseif($c->dimension == 2)
					$result .= '<div class="6u 12u$(small)">';
				elseif($c->dimension == 3)
					$result .= '<div class="4u 12u$(small)">';
				elseif($c->dimension == 4)
					$result .= '<div class="3u 12u$(small)">';
				else
					$result .= '<div
					>';
				
				$result .= '<header class="'.$c->class_header.'">';
				$result .= '<h1>'.$c->title_header.'</h1><p>'.$c->sub_title_header.'</p>';
				$result .= '<header>';
				
				$result .= $c->text_html;
				$result .= '</div>';
			}
			$result .= '</div>';
			if(strcmp($content, "footer") == 0)
				$result .= '</footer>';
			else
				$result .= '</section>';
		}
		
		return $result;
	}*/

}

?>
