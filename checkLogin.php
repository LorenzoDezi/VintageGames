<?php 

      /*
        Lo script CheckLogin.php viene utilizzato all'inizio della pagina per
        controllare tentativi di login e di logout, e agire di conseguenza.
      */
    
    //Controlliamo se l'utente vuole fare logout (form della pagina logOut.php)
      if(isset($_POST["log_out"])) {
        require "logOut.php";
      }
      else if (isset($_POST["username_log"]) && isset($_POST["password_log"])) {
  		//Controlliamo la richiesta di login
  		//è un "else" in quanto non può avvenire se si è loggati
  		//l'md5 evita di scrivere la password sul db in "plain text"
  		$password = md5($_POST["password_log"]);
  		$username = test_input($_POST["username_log"]);
  		$always_log = false;
  		if(isset($_POST["always_log"]))
  			$always_log = true;
  		//se $_POST["always_log"] sta a true, i cookie avranno scadenza ad un anno
  		$logInResult = logIn($username, $password, false, $always_log);
  		if ($logInResult != "Authenticated")
  			//C'è stato un errore nel login, non è stata creata la sessione
  			//$_SESSION["log_fail"] sarà utilizzata dalla form di login
  			$_SESSION["log_fail"] = $logInResult;	
      } 
  ?>