<!-- Questa introduzione in php riguarda l'aggiornamento della sessione -->
<?php
$logInResult = "";
session_start();
require "testInputFunction.php";
require "checkForCookie.php";
require "updateSession.php";
require "checkLogin.php"; 
?>


<!DOCTYPE html>
<html>
<head>
	<title>Pong</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <!--La div classe container contiene tutti gli altri elementi ad 
    	eccezione del footer 
    -->
	<div class="container">

		<!--La div classe punteggioDiv contiene il punteggio -->
		<div class="scoreDiv">
		    <p id="pongScore">
		     Score = 0
		    </p>
		    <?php if($logInResult == "")
		            echo "<p> You're not logged - Can't save your score </p>";
				  else if ($logInResult == "Error connecting the db.")
                  	echo "<p> Error connecting the db - Can't save your score </p>";
		          else if ($logInResult != "Authenticated")
		          	echo "<p> You're not authenticated - Can't save your score </p>"; 
		    ?>
	    </div>

	    <!-- La div classe description contiene una breve descrizione della pagina -->
	    <div class="description">
		    <p>
		      Use right arrow and left arrow to move your paddle (the one at the bottom). 
		      Increase the ball speed to win! 
		    </p>
	    </div>
	    <!-- La div classe gioco contiene uno dei giochi del blog  -->
		<div class ="game">
			<canvas id="pong" height="500" width="300" ></canvas>
		</div>
		<!-- La div classe loginAreaGame ha una formattazione specifica css
			 per i giochi, a differenza di index.php 
		-->
		<div class = "loginAreaGame">
	      <?php require "loginArea.php";?>
	    </div>
    </div>
    <!-- Il footer si trova sempre in fondo alla pagina html, con posizione assoluta -->
    <footer>Copyright 2016 Lorenzo Dezi, Vittorio Cipriani.</footer>

    <!-- I vari audio utilizzati dal gioco -->
	<audio id="loseSound" src="Media/Lose.wav"></audio>
	<audio id="winSound" src="Media/Win.wav"></audio>
	<audio id="bounceSound" src="Media/Bounce.wav"></audio>
	<audio id="touchSound" src="Media/Touch.wav"></audio>

	<!-- I vari script utilizzati dal gioco -->
	<script  src="jquery-3.0.0.min.js"></script>
    <script type="text/javascript" src="connectionControl.js"></script>
	<script src="Pong.js"></script>
	
</body>
</html>