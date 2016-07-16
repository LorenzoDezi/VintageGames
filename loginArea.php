
<?php
	/*
		Questo script php genera codice html che verrà riutilizzato
		in più pagine del sito. Il codice sarà diverso se l'utente è
		loggato oppure no.
	*/
	if(!isset($_SESSION["logged"])) {
		//L'utente non è loggato
		//Questo primo controllo riguarda i tentativi di login falliti
		if(isset($_SESSION["log_fail"])) {

			echo "<span class='error'>".$_SESSION["log_fail"]."</span>";
			unset($_SESSION["log_fail"]);
			
		}
		//Il codice  della form che permette di inserire l'username e la
		//password del login.
		echo 
			"<form method='post' action=".htmlspecialchars($_SERVER["PHP_SELF"]).">
			<p>Username: <input  type='text' name='username_log'></p>
			<p>Password: <input type='password' name='password_log'></p>
			<p >Always logged:<input type='checkbox' name='always_log'></p>
			<input type='submit' value='Login'>
			<br>
			<p>Wanna register? <a href='registration.php'>Click here</a></p>
			</form>";

	} else if ($_SESSION["logged"]) {
		//se l'utente è loggato, gli si dà la possibilità di fare logOut
		echo "<form method='post' action=".htmlspecialchars($_SERVER["PHP_SELF"]).">
			  <p>".$_SESSION["username"].", you are logged in. \n";
		//si controllano i tentativi di logout falliti
 		if(isset($_SESSION["log_out_fail"])) {
 			echo "<span class='error'>".$_SESSION["log_out_fail"]."</span>";
 			unset($_SESSION["log_out_fail"]);
 		}
 		//l'input "hidden" viene utilizzato per mandare dati al link della form
 		//implicitamente		
 		echo "</p> 
 			<input type='hidden'  name='log_out' value=true>
			<input type='submit' value='Logout'>
			</form>";
		//Si controlla il fallimento di un possibile aggiornamento di sessione
		//Oppure si mostra un messaggio se non si è ancora
		if($logInResult != "Authenticated")
			//Mostra il messaggio di risultato del login (o dell'aggiornamento di sessione) 
			echo "<p>".$logInResult."</p>";
	}
?>