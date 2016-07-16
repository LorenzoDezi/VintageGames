<?php 
	
	/* 
		Questo script permette di aggiornare il punteggio dell'utente
		se autenticato. Viene richiamato nei javascript dei giochi attraverso
		jquery.
	*/

	session_start();
	try {
	require "dbConn.php";
	$query = "";
	$checkQuery = "";
	$score = 0;
	$fieldDB = "";
	//Controlla se l'utente è loggato e cambia il nome delle variabili e la query
	//a seconda del gioco da cui proviene la richiesta
	if((isset($_GET["snake"])) && isset($_SESSION["logged"])) {
		$checkQuery = "SELECT * FROM users WHERE username = '".$_SESSION["username"]."';";
		$score = $_GET["snake"];
		$fieldDB = "record_snake";
	} else if(isset($_GET["pong"]) && isset($_SESSION["logged"])) {
		$checkQuery = "SELECT * FROM users WHERE username = '".$_SESSION["username"]."';";
		$score = $_GET["pong"];
		$fieldDB = "record_pong";
	}
	//Se l'utente è loggato checkQuery avrà un valore
	if($checkQuery != "") {
		//Si ritorna la riga di database associata 
		$result = $conn -> query($checkQuery);
	    $row = $result->fetch(PDO::FETCH_ASSOC);
			//Solo se autenticato l'utente potrà salvare il punteggio
			if($row["flag_authenticated"]) {
				$bestScore = $row[$fieldDB];
				$query = "UPDATE users SET ".$fieldDB." = '".$score."' WHERE username= '".$_SESSION["username"]."';";
				//Il punteggio sarà aggiornato solo se esso sarà il migliore
				if($score > $bestScore) {
					$conn->query($query);
				}
			}
	}
	//si chiude la connessione e si comunica il risultato(errore/no)
	$conn = null;
	echo "NoError";
	} catch (PDOException $e) {
		echo "Can't connect to the db! Try later!";
	}
		
?> 