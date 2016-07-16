<?php 
	/*
		La funzione logIn è necessaria sia alla registrazione, che ai vari login e all'aggiornamento di sessione.
		La variabile di ritorno sarà un messaggio che indica l'esito del login. Accetta in input username, password,
		sessione (se non è false vuol dire che è un aggiornamento di sessione, altrimenti è sempre un id unico) e 
		always_logged che indica il tempo di expiration dei cookie. Il login crea la sessione, aggiorna il db mysql e 
		crea i cookie. Se sta facendo un aggiornamento di sessione, non è necessario che essa aggiorni anche $_SESSION.
	*/
	function logIn($username, $password, $session = false, $always_logged = false) {

		$return_message = "";
		try {
			require "dbConn.php";
			if($session === false) {
				$session = uniqid();
			}
			$checkQuery = "SELECT count(*) FROM users WHERE username = '".$username."' and password = '".$password."';";
			$result = $conn -> query($checkQuery);
			$numRows = $result -> fetchColumn();
			if($numRows == 1) {
				//E' presente un utente con quell'username, il login è valido
				//Tiro fuori la sessione dell'utente connesso.
				$query = "SELECT * FROM users WHERE username = '".$username."' and password = '".$password."';";
				$row = $conn -> query($query) -> fetch(PDO::FETCH_ASSOC);
				if($session != $row["session_id"]) {
					//Se la sessione corrente è diversa da quella nel db,
					//allora è un primo accesso o la sessione è diversa
					//nel primo caso $row['time_session'] = 0, nel secondo
					//bisognerà controllare che siano passati 20 minuti dall'ultimo
					//accesso.
					if ($row["time_session"] < time() - (20*60)) {
						//in tal caso si può fare il login
						//si aggiorna la sessione sul db
						$query = "UPDATE users SET session_id = '".$session."', time_session = '".time()."' WHERE username = '".$username."';";
						$conn -> query($query);
						//si calcola l'expiration_time dei cookie in base a $always_logged
						//se a true, il tempo di expiration sarà un anno
						$expiration_time = 20*60;
						if($always_logged)
							$expiration_time = 365*24*60*60;
						//si settano i cookie
						setcookie("usernameGB", $username, time() + $expiration_time);
						setcookie("sessionIDGB", $session, time() + $expiration_time);
						setcookie("passwordGB", $password, time() + $expiration_time);
						setcookie("always_logged", $always_logged, time() + $expiration_time);
						//si setta la sessione
						$_SESSION["username"] = $username;
						$_SESSION["password"] = $password;
						$_SESSION["always_logged"] = $always_logged;
						$_SESSION["expiration"] = $expiration_time;
						$_SESSION["logged"] = true;
						$_SESSION["session_id"] = $session;
						//Controllo autenticazione e ritorno login positivo
						if($row["flag_authenticated"])
							$return_message = "Authenticated";
						else 
							//Si ritorna il messaggio di non autenticazione con link
							$return_message = "You're not authenticated yet.<a href='confirmationCode.php'>Click here</a>";
					} else
						//Non è passato abbastanza tempo dall'ultimo login effettuato
						//quindi il login non è valido 
						$return_message = "A user with this username is already connected. Try again within 20 minutes.";
				} else 
					{
					//$session == $row["session_id"], quindi
					//E' la stessa sessione a fare il login, significa che è in corso un aggiornamento di sessione
					//Aggiorniamo il tempo sul database
					$query = "UPDATE users SET session_id = '".$session."', time_session = '".time()."' WHERE username = '".$username."';";
					$conn -> query($query);
					//In questo caso basta aggiornare i cookie, non è necessario un aggiornamento di sessione
					$expiration_time = 20*60;
					if($always_logged)
						$expiration_time = 365*24*60*60;
					setcookie("usernameGB", $username, time() + $expiration_time);
					setcookie("sessionIDGB", $session, time() + $expiration_time);
					setcookie("passwordGB", $password, time() + $expiration_time);
					setcookie("always_logged", $always_logged, time() + $expiration_time);
					if($row["flag_authenticated"])
						$return_message = "Authenticated";
					else 
						$return_message = "You're not authenticated yet.<a href='confirmationCode.php'>Click here</a>";
					}
				} else {
					//Non esiste alcun username
						$return_message = "Username or password wrong!";
				}
				//Si chiude la connessione col db
				$conn = null;
	} catch (PDOException $e) {

		$return_message = "Error connecting the db.";
	}
	return $return_message;
	}

?>
