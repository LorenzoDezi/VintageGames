<?php 
	//Controlla l'esistenza dei cookie e crea la sessione se necessario 
	if(isset($_COOKIE["usernameGB"])) {
		$_SESSION["logged"] = true;
		$_SESSION["username"] = test_input($_COOKIE["usernameGB"]);
		$_SESSION["password"] = $_COOKIE["passwordGB"];
		$_SESSION["session_id"] = $_COOKIE["sessionIDGB"];
		if(isset($_COOKIE["always_logged"]))
			$_SESSION["always_logged"] = true;
		
	}

?>