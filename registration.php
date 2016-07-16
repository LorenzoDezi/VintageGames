<!-- Il codice per il form della registrazione -->
<html>
<head>
	<title>Registrazione GameBlog</title>
 	<link rel="stylesheet" href="style.css">
</head>
<body>
	
	<?php 
		//in caso il checkRegistration.php avesse notato qualche errore
		//esso rimanderà alla pagina di registrazione e POSTerà gli errori
		//e ciò che si è scritto in precedenza nei tag input.
		session_start();
		$username = "";
		if(isset($_SESSION["username"])) {
			$username = $_SESSION["username"];
		}
		$usernameErr = "";
		if(isset($_SESSION["usernameErr"])) {
			$usernameErr = $_SESSION["usernameErr"];
		}
		$pwdErr = "";
		if(isset($_SESSION["pwdErr"])) {
			$pwdErr = $_SESSION["pwdErr"];
		}
		$email = "";
		if(isset($_SESSION["email"])) {
			$email = $_SESSION["email"];
		}
		$emailErr = "";
		if(isset($_SESSION["emailErr"])) {
			$emailErr = $_SESSION["emailErr"];
		}
		$repPwdErr = "";
		if(isset($_SESSION["repPwdErr"])) {
			$repPwdErr = $_SESSION["repPwdErr"];
		}
		//Le variabili errore sono svuotate per prevenire problemi di errori salvati nella
		//sessione
		$_SESSION["usernameErr"] = $_SESSION["repPwdErr"] = $_SESSION["pwdErr"] = $_SESSION["emailErr"] = "";

	?>
	<!--La div classe container contiene tutti gli altri elementi ad 
      eccezione del footer 
    -->
	<div class="container">
	<!-- La form utilizza il metodo post in quanto invia dati sensibili -->
	<form method="post" action="checkRegistration.php">
	  <!-- Abbiamo utilizzato una table per dare una struttura alla form -->
	  <table>
	  <tr>
		  <td>
		  	<p><span class="error">* required field.</span></p>
		  </td>
	  </tr>
	  <tr>  
		  <td>Username: </td>
		  <td><input type="text" name="username_reg" value="<?php echo $username;?>"></td>
		  <td>* <?php echo $usernameErr;?></td>
	  	  <br><br>
	  </tr>
	  <tr>
		  <td>Password: </td>
		  <td><input type="password" name="password_reg" value=""></td>
		  <td>* <?php echo $pwdErr; ?></td>
		  <br><br>
	  </tr>
	  <tr>
		  <td>Repeat password  </td>
		  <td><input type="password" name="repeat_password_reg" value=""></td>
		  <td>* <?php echo $repPwdErr; ?></td>
		  <br><br>
	  </tr>
	  <tr>
		  <td>E-mail:  </td>
		  <td><input type="text" name="email_reg" value="<?php echo $email;?>"></td>
		  <td>* <?php echo $emailErr;?></td>
		  <br><br>
	  </tr>
	  </table>
	  <input type="submit" name="submit" value="Submit">  
	</form>
	<?php
		//Gestisce errori di db/email scaturiti da checkRegistration.php
		if(isset($_GET["Error"]))
			echo "<span class='error'>".$_GET["Error"]."</span>"; 
	?>
	</div>
	<!-- Il footer si trova sempre in fondo alla pagina html, con posizione assoluta -->
	<footer>Copyright 2016 Lorenzo Dezi, Vittorio Cipriani.</footer>

</body>
</html>