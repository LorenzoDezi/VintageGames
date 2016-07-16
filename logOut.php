<?php 
		/* 
			Questo script php viene utilizzato per fare il log-out dell'utente 
			connesso.
		*/


		//Controllo sul login dell'utente
		if(isset($_SESSION["logged"])) {
			//Si mette il tempo di sessione a zero: ciò implica
			//che chiunque provi a fare il login dopo, può connettersi (vedi loginFunction.php)
			$sqlUpdate = "UPDATE users SET time_session = 0 WHERE username = '".$_SESSION["username"]."';";
			try {
		  	require "dbConn.php";
		  	$conn->query($sqlUpdate);

		  	//Vengono distrutti i cookie e la sessione
		  	setcookie("usernameGB", $_SESSION["username"], time() - 1);
		  	setcookie("passwordGB", $_SESSION["password"], time() - 1);
			setcookie("sessionIDGB", $_SESSION["session_id"], time() - 1);
			if(isset($_SESSION["always_logged"]))
				setcookie("always_logged", true, time() - 1);
			session_unset();
		  	session_destroy();
		  	$conn = null;
		  	//Viene fatto il refresh della pagina
		  	header("Location: ".htmlspecialchars($_SERVER["PHP_SELF"]));
	  		}
	  		catch (PDOException $e) {
		  		//La query ha fallito, si utilizza la variabile
		  		//$dbError dichiarata in dbconn.php
			  	$_SESSION["log_out_fail"] = "Can't logout cause db connection problem";
			  	$conn = null;
		  	}
	  	} 
?>