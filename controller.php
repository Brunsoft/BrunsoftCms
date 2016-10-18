<?php
class Controller
{
    public $model;

    public function __construct($model){
        $this->model = $model;
    }
    
    public function getPage($url){
		if(strcmp(substr($url, -1), "/") != 0)
			$url .= '/';
			
		$pages = explode("/", $url);
 		
 		$id_page = "";
		$page_ok = 0;
		for($i=count($pages)-2; $i>=0; $i--){
			$page = $this->model->getPageSel($pages[$i]);
			if($page->id_page != null){
				$main_page = null;
				if($i > 0){
					$main_page = $this->model->getPageSel($pages[$i-1]);
					if($main_page->id_page != null){
						if($page->main_page == $main_page->id_page){
							$page_ok ++;
							//echo '<br/>'.$pages[$i];
						}
					}
				}else{
					if($page->main_page == null){
						$page_ok ++;
						//echo '<br/>'.$pages[$i];
					}
				}
			}			
		}
		
		if($page_ok == count($pages)-1){
			$this->model->init($pages[count($pages)-2]);
    		return true;
		}
		return false;	
	}
}
?>