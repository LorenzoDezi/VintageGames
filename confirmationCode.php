<!-- confirmationCode permette di inserire il codice di conferma -->
<html>
	<head>
		<title>ConfirmationCode</title>
 		<link rel="stylesheet" href="style.css">
	</head>
	<body>
	 <?php
	session_start();
	if (!isset($_SESSION["logged"]))
		//se l'utente non è loggato, non ha senso stare su questa pagina
		//e c'è il redirect alla pagina principale
	    header("Location: index.php");
	//la form permette di inserire il codice di autenticazione inviato tramite email
		echo "<div class='container'>
				<form id='auth' method='post' action='index.php'>
	                <p>Insert your authentication code (send by email): \n</p>
	                <input type='text' name='auth_code' value=''></input>
	                <input type='submit' value='Verify'>
	          	</form>
	          	<button onclick = \" window.location.href = 'index.php' \"
	          	id = 'exitAuth'>
	          	 Exit </button>";

	echo "</div>";
	?> 
	<footer>Copyright 2016 Lorenzo Dezi, Vittorio Cipriani.</footer>
	</body>

</html>