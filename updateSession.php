<?php 
	//Questo script controlla la sessione aggiornando i cookie e il database.
	//va richiamata ad ogni azione dell'utente (cambio di pagina)
	require("loginFunction.php");
	if(session_status() == PHP_SESSION_NONE)
		session_start();
	if(isset($_SESSION["logged"])) {
		$always_logged = false;
		if(isset($_SESSION["always_logged"]))
			$always_logged = true;
		$logInResult = logIn($_SESSION["username"],$_SESSION["password"],$_SESSION["session_id"],$always_logged);
		if($logInResult != "Authenticated"  && $logInResult != "You're not authenticated yet.<a href='confirmationCode.php'>Click here</a>") {
			session_unset();
			session_destroy();
		}	
	}

?>