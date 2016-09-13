<?php
	function sec_session_start() {
        $session_name = 'sec_session_id'; 				// Imposta un nome di sessione
        $secure = false;	 							// Imposta il parametro a true se vuoi usare il protocollo 'https'.
        $httponly = true; 								// Questo impedirà ad un javascript di essere in grado di accedere all'id di sessione.
        ini_set('session.use_only_cookies', 1); 		// Forza la sessione ad utilizzare solo i cookie.
        $cookieParams = session_get_cookie_params(); 	// Legge i parametri correnti relativi ai cookie.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name); 					// Imposta il nome di sessione con quello prescelto all'inizio della funzione.
        session_start(); 								// Avvia la sessione php.
        session_regenerate_id(); 						// Rigenera la sessione e cancella quella creata in precedenza.
	}
	
	function login($email, $password, $mysqli) {
	   if ($stmt = $mysqli->prepare("SELECT id_user, username, pwd, salt FROM user WHERE email = ? LIMIT 1")) { 
			$stmt->bind_param('s', $email); 
			$stmt->execute(); 
			$stmt->store_result();
			$stmt->bind_result($user_id, $username, $db_password, $salt); 
			$stmt->fetch();
			$password = hash('sha512', $password.$salt); 
			if($stmt->num_rows == 1) {
				
				if($db_password == $password) {    
					$user_browser = $_SERVER['HTTP_USER_AGENT']; 
					$_SESSION['user_id'] = $user_id; 
					$_SESSION['username'] = $username;
					$_SESSION['login_string'] = hash('sha512', $password.$user_browser);
					
					return true;    
				}else
					$_SESSION['message'] = 'Password errata';
			}else
				$_SESSION['message'] = 'Utente non trovato';
		}else
		    $_SESSION['message'] = 'Errore';
		    
		return false;
	}
	
	function login_check($mysqli) {  
	  if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
	     $user_id = $_SESSION['user_id'];
	     $login_string = $_SESSION['login_string'];
	     $username = $_SESSION['username'];     
	     $user_browser = $_SERVER['HTTP_USER_AGENT']; 
	     if ($stmt = $mysqli->prepare("SELECT pwd FROM user WHERE id_user = ? LIMIT 1")) { 
	        $stmt->bind_param('i', $user_id); 
	        $stmt->execute(); 
	        $stmt->store_result();
	 
	        if($stmt->num_rows == 1) { 
	           $stmt->bind_result($password); 
	           $stmt->fetch();
	           $login_check = hash('sha512', $password.$user_browser);
	           if($login_check == $login_string)
	              return true;
	           else
	              return false;
	        }else
	            return false;
	     }else
	        return false;
	   }else
	     return false;
	}
?>