<?php
require_once('article.php');
require_once('widget.php');

class Page{
	public $id_page;
    public $name_page;
    public $descr;
    public $title;
    public $type;		// Single Article, Blog
    public $category_articles;
    public $permaname;
    public $main_page;
    public $date_creation;
    public $date_last_edit;
    public $user_author;
    public $user_last_edit;
    public $published;
    public $ord;
    public $home;
    public $menu;		// Array oggetti Widget pos menu
    public $logo;		// Array oggetti Widget pos logo
    public $banner;		// Array oggetti Widget pos banner
    public $top_a;		// Array oggetti Widget pos topA
    public $top_b;		// Array oggetty Widget pos topB
    public $main;		// Array oggetti Articolo pos main
    public $bottom_a;	// Array oggetti Widget pos bottomA
    public $bottom_b;	// Array oggetti Widget pos bottomB
    public $footer;		// Array oggetti Widget pos footer

	public $permalink;
       
    public function __construct($id_page, $mysqli){
		$this->init($id_page, $mysqli);
		
    }
    
    public function init($id_page, $mysqli){
    	if ($stmt = $mysqli->prepare("SELECT name_page, title, descr, type, category_articles, permaname, main_page, date_creation, date_last_edit, user_author, user_last_edit, published, ord, home FROM page WHERE id_page = ?")) {
			$stmt->bind_param('s', $id_page);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_page, $title, $descr, $type, $category_articles, $permaname, $main_page, $date_creation, $date_last_edit, $user_author, $user_last_edit, $published, $ord, $home);
			$stmt->fetch(); 
			if ($stmt->affected_rows > 0){
				$this->id_page = $id_page;
				$this->name_page = $name_page;
				$this->title = $title;
				$this->descr = $descr;
				$this->type = $type;
				$this->category_articles = $category_articles;
				$this->permaname = $permaname;
				$this->main_page = $main_page;
				$this->date_creation = $date_creation;
				$this->date_last_edit = $date_last_edit;
				$this->user_author = $user_author;
				$this->user_last_edit = $user_last_edit;
				$this->published = $published;
				$this->ord = $ord;
				$this->home = $home;
			}
			$this->permalink = $this->getPermalink($this->main_page, $mysqli).'/';
			$this->menu = array();
			$this->banner = array();
			$this->top_a = array();
			$this->top_b = array();
			$this->main = array();
			$this->bottom_a = array();
			$this->bottom_b = array();
			$this->footer = array();
			$this->findContent($this->id_page, $mysqli);
		}
	}

	private function findContent($id_page, $mysqli){
		// Cerco eventuali Widget
    	if ($stmt = $mysqli->prepare("SELECT w.name_widget, w.pos FROM widget_page wp JOIN widget w ON wp.name_widget = w.name_widget WHERE wp.id_page = ? ORDER BY w.ord")) {
			$stmt->bind_param('i', $id_page);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_widget, $pos);
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					if(strcmp($pos, "banner") == 0)
						array_push($this->banner, new Widget($name_widget, $mysqli));
					elseif(strcmp($pos, "topA") == 0){
						array_push($this->top_a, new Widget($name_widget, $mysqli));
					}elseif(strcmp($pos, "topB") == 0)
						array_push($this->top_b, new Widget($name_widget, $mysqli));
					elseif(strcmp($pos, "bottomA") == 0)
						array_push($this->bottom_a, new Widget($name_widget, $mysqli));
					elseif(strcmp($pos, "bottomB") == 0)
						array_push($this->bottom_b, new Widget($name_widget, $mysqli));
					elseif(strcmp($pos, "footer") == 0)
						array_push($this->footer, new Widget($name_widget, $mysqli));
					elseif(strcmp($pos, "menu") == 0)
						array_push($this->menu, new Widget($name_widget, $mysqli));
					elseif(strcmp($pos, "logo") == 0)
						array_push($this->logo, new Widget($name_widget, $mysqli));
				}
			}
		}
		// Cerco eventuali articoli
		if ($stmt = $mysqli->prepare("SELECT name_article FROM article_page WHERE id_page = ?")) {
			$stmt->bind_param('i', $id_page);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name_article);
			if ($stmt->affected_rows > 0){
				while ($stmt->fetch()) {
					array_push($this->main, new Article($name_article, $mysqli));
				}
			}
		}
	}
	
	private function getPermalink($page, $mysqli){
		$result = "";
		$mainpage = "";
		if(strcmp($page, "")!=0){
			if ($stmt = $mysqli->prepare("SELECT main_page, permaname FROM page WHERE id_page = ?")) {
				$stmt->bind_param('s', $page); 
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($main_page, $permaname); 
				$stmt->fetch();
				if ($stmt->affected_rows > 0){
					$mainpage .= $main_page;
					$result = '/'.$permaname;
				}
						
			}
		}else
			return $result;
			
		if(strcmp($mainpage, "") != 0)
			$result = $this->getPermalink($mainpage, $mysqli).$result;

		return $result;
	}
	
	public function toString($content, $mysqli, $logged){
		$result = "";
		$contents = array();
		$class = "";
		if(strcmp($content, "banner") == 0){
			$contents = $this->banner;
			//$class = $this->class_banner;
		}elseif(strcmp($content, "topA") == 0){
			$contents = $this->top_a;
			//$class = $this->class_top_a;
		}elseif(strcmp($content, "topB") == 0){
			$contents = $this->top_b;
			//$class = $this->class_top_b;
		}elseif(strcmp($content, "main") == 0){
			$contents = $this->main;
			//$class = $this->class_main;	
		}elseif(strcmp($content, "bottomA") == 0){
			$contents = $this->bottom_a;
			//$class = $this->class_bottom_a;	
		}elseif(strcmp($content, "bottomB") == 0){
			$contents = $this->bottom_b;
			//$class = $this->class_bottom_b;	
		}elseif(strcmp($content, "footer") == 0){
			$contents = $this->footer;
			//$class = $this->class_footer;	
		}elseif(strcmp($content, "menu") == 0){
			$contents = $this->menu;
			//$class = $this->class_footer;	
		}
		
		//echo count($contents);
		if(count($contents) > 0){
			if(strcmp($content, "menu") == 0){
				foreach($contents as $c){
					$sub_menu = $c->sub_menu;
					foreach($sub_menu as $sb){
						if($sb->url == null ){
							$permalink = '/mvc'.$this->getPermalink($sb->link, $mysqli);
							$result .= '<a href="'.$permalink.'">'.$sb->name_sub_menu.'</a>';
						}elseif($sb->link == null){
							if(($sb->url == '/bs-login.php' && !$logged) || ($sb->url == '/bs-admin/logout.php' && $logged))
								$result .= '<a href="/mvc'.$sb->url.'">'.$sb->name_sub_menu.'</a>';
						}
							
					}
				}
				
			}elseif(strcmp($content, "main") == 0){
				if($this->category_articles == null){	// Single Article or Hidden article
					if($contents[0]->published == 1){
						$result .= '<section id="'.$content.'" class="wrapper style1 special">';
						$result .= '<div class="inner">';
					
						$result .= $contents[0]->html;
						
						if($logged && count($contents[0]->comments)>0){
							
							$result .= '<hr/><table class="alt comment"><tbody>';
							
							foreach($contents[0]->comments as $comment){
								$result .= '<tr><td width=50><img class="img-user" src="images/user.gif" width=50 alt=""></td><td width=200>'.$comment->username.'<br/>'.$comment->date.'</td><td>'.$comment->html.'</td></tr>';
							}
							$result .= '</tbody></table>';
						}
						if($logged){
							$result .= 'Inserisci un commento';
							$result .= '<form method="post" action="">';
							$result .= '<input type="hidden" name="name_article" id="name_article" value="'.$contents[0]->name_article.'" >';
							$result .= '<textarea name="html_article" id="html_article" placeholder="Commento" rows="4" ></textarea>';
							$result .= '<input type="submit" class="button special small" name="inserisci" value="Inserisci" style="float: right;">';
							$result .= '</form>';
						}
						
						$result .= '</div>';
						$result .= '</section>';
					}
				}
				
			}else{
				if(strcmp($content, "footer") == 0){
					$result .= '<footer id="'.$content.'" class="wrapper">';
					$result .= '<div class="inner">';	
				}else{
					$result .= '<section id="'.$content.'" class="wrapper">';
					$result .= '<div class="inner">';
				}
					
				$result .= '<div class="row">';
				
				foreach($contents as $c){
					if($c->published == 1){
						if($c->dim == 1)
							$result .= '<div class="12u 12u$(small)">';
						elseif($c->dim == 2)
							$result .= '<div class="6u 12u$(small)">';
						elseif($c->dim == 3)
							$result .= '<div class="4u 12u$(small)">';
						elseif($c->dim == 4)
							$result .= '<div class="3u 12u$(small)">';
						else
							$result .= '<div>';	
						$result .= $c->html;
						$result .= '</div>';
					}
				}
				$result .= '</div></div>';
				if(strcmp($content, "footer") == 0)
					$result .= '</footer>';
				else
					$result .= '</section>';
			}
		}
			
		
		return $result;
	}

}

?>
