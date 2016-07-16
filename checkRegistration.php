<?php 
	
	/*
		Lo script checkRegistration controlla i dati inseriti nella form
		di registration.php e reindirizza l'utente indietro sulla form se ha 
		commesso degli errori (visualizzandoli), oppure passa alla pagina di 
		inserimento del codice di autenticazione. 
	*/
	require "loginFunction.php";
    require "testInputFunction.php";

	/* 
		La funzione make_unique è utiizzata per creare un codice
		unico per l'autenticazione.
	*/
	function make_unique($length=16) 
	{
           $salt       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ012345678';
           $len        = strlen($salt);
           $makepass   = '';
           mt_srand(10000000*(double)microtime());
           for ($i = 0; $i < $length; $i++) {
               $makepass .= $salt[mt_rand(0,$len - 1)];
           }
       	   return $makepass;
	}

	/* 
		La funzione send_email utilizza le classi phpmailer e smtp 
		importate all'interno del progetto
	*/
	function send_email($email,$auth_code) {

		//Aggiungiamo il codice della classe PHPMailer e SMTP
		require "phpmailer/class.phpmailer.php";
		require "phpmailer/class.smtp.php";
		//rendiamo accessibile la variabile globale
		//Creo un oggetto PHPMailer e configuro il SMTP
		$mail = new PHPMailer;		
		$mail ->isSMTP();
		$mail ->Host = "smtp.gmail.com";
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "tls";                 
		$mail->Port       = 587;                   
		$mail->Username   = "gameblog.noreply@gmail.com";  
		$mail->Password   = "Arrivederci1";            
		$mail->FromName = "GameBlog";
		//To address and name
		$mail->addAddress($email);
		$mail->isHTML(true);

		$mail->Subject = "Codice di autenticazione gameBlog";
		$mail->Body = wordwrap("<i>Ecco il codice di autenticazione necessario all'iscrizione al nostro sito. Grazie per l'iscrizione!!
				".$auth_code."</i>",70,"\n",false);
		$mail->AltBody = "Codice di autenticazione";

		return $mail->send();

	}

	session_start();
	//Le variabili seguenti sono utilizzati per memorizzare dati e possibili errori
	$username = $usernameErr = $pwdErr = $repPwdErr = $email = $emailErr = "";
	//la variabile $error viene utilizzata come flag per gli errori
	$error = false;
	try {
		require "dbConn.php";
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			//Controlliamo lo username
			if (empty($_POST["username_reg"])) {	
			  $usernameErr = "Name is required";
			  $error = true;
			} else {
				//facciamo il test_input e controlliamo che 
				//lo stesso username non sia già stato utilizzato
			  	$username = test_input($_POST["username_reg"]);
				$query = "SELECT COUNT(*) FROM users WHERE username ='".$username."';";
				$numRows = $conn -> query("$query") -> fetchColumn();
				if($numRows == 1) {
					$usernameErr = "Username already used!";
					$error = true;
				}
			  	else if(strlen($username) > 20) {
			  		//lo username non può superare i 20 caratteri
				  	$usernameErr = "At most 20 characters for the username";
				  	$error = true;
			  	}
			}
			//Controlliamo la password
			if (empty($_POST["password_reg"])) {
			  $pwdErr = "Password is required";
			  $error = true;
			} else {
				
				$password = $_POST["password_reg"];
				if (strlen($password) < 8 || strlen($password) > 30) {
			    	$pwdErr = "The password must be at least 8 characters long and at most 30";
			    	$error = true;
			  	} else if (preg_match("[^\s]", $password)) {
			  		//controlliamo tramite regex l'assenza di spazi bianchi
			  		$pwdErr = "The password cannot contain white spaces";
			  		$error = true;
			  	} 
			}
			//Controllo la seconda riscrittura della password
			if(empty($_POST["repeat_password_reg"]) || $_POST["password_reg"] != $_POST["repeat_password_reg"]) {
				$repPwdErr = "You must rewrite the same password";
				$error = true;
			} 
			//Controllo l'email
			if(empty($_POST["email_reg"])){
				$emailErr = "Email is required";
				$error = true;  
			} else {
				$email = $_POST["email_reg"];
				//uso la funzione standard filter_var di php per l'email
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
	      			$emailErr = "Invalid email format";
	      			$error = true;
	    		}
			}
			if($error) {
				//Imposto la sessione con gli errori e
				//i campi già compilati, e ritorno alla form 
				$_SESSION["username"] = $username;
				$_SESSION["usernameErr"] = $usernameErr;
				$_SESSION["pwdErr"] = $pwdErr;
				$_SESSION["repPwdErr"] = $repPwdErr;
				$_SESSION["email"] = $email;
				$_SESSION["emailErr"] = $emailErr;
				header("Location: registration.php");
			} else {
				//creo il codice di autenticazione unico
				$auth_code = make_unique(8);
				//cerco di inviare l'email con il codice
				if(send_email($email,$auth_code)) {
					//Se l'email viene inviata correttamente, si registra nel database la row 
					//associata all'utente
					$password = md5($password);
					$sql = "INSERT INTO users(username,password,email,auth_code) VALUES ('".$username."','".$password."','".$email."','".$auth_code."');";
					$conn->query($sql);
					$logInResult = logIn($username, $password);
					//Il risultato, a questo punto del codice, può essere solo 'Not authenticated'.
					if($logInResult == "You're not authenticated yet.<a href='confirmationCode.php'>Click here</a>") {
						$conn = null;
						header("Location: confirmationCode.php");
					}
				}
				else {
					//C'è stato un errore nell'invio dell'email
					//Imposto la sessione con i campi inseriti
					$_SESSION["username"] = $username;
					$_SESSION["email"] = $email;
					$conn = null;
					//Ritorno al sito della registrazione con l'errore nella queryString
					$emailErr = "error sending email for authentication";
					header("Location: registration.php?Error=".$emailErr);
				}
			}

		} else 
			header("Location: index.php");
	} catch (PDOException $e) {
		//C'è stato un errore nell'esecuzione della query
		//Imposto la sessione con i campi inseriti
		$_SESSION["username"] = $username;
		$_SESSION["email"] = $email;
		$conn = null;
		//Ritorno al sito originale con l'errore nella queryString
		header("Location: registration.php?Error=Can't connect to the db! Try later");
	}
?>
