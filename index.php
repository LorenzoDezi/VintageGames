
<!-- Questo script riguarda il controllo della sessione e del codice di autenticazione -->
<?php


  //Si fa partire la sessione
  session_start();
  //LogInResult serve per immagazzinare gli errori relativi a login/autenticazioni falliti
  $logInResult = "";
  //Vengono aggiunti i vari script php di controllo iniziale e la funzione di login
  require "testInputFunction.php";
  require "checkForCookie.php";
  require "updateSession.php";
  require "checkLogin.php";
  //Controlliamo se l'utente è loggato
  if(isset($_SESSION["logged"])) {
	  //Controlliamo la richiesta di autenticazione
	  if(isset($_POST["auth_code"])) {
      try {
	  	require "dbConn.php";
	  	$sqlCheck = "SELECT * FROM users WHERE username = '".$_SESSION["username"]."';";
	  	$row = $conn -> query($sqlCheck)->fetch(PDO::FETCH_ASSOC);
      if($_POST["auth_code"] == $row["auth_code"]) {
          //il flag_authenticated del database serve per definire se l'utente è autenticato oppure no
          $sqlUpdate = "UPDATE users SET flag_authenticated = true WHERE username = '".$_SESSION["username"]."';";
          //controllo di successo della query
          $conn -> query($sqlUpdate);
            $logInResult = "Authentication OK!";
          } else 
              $logInResult = "Authentication failed: authentication code wrong!";
          $conn = null;
      } catch(PDOException $e) {
          $logInResult = "Error connecting the database";
      }
	  }
  }
  
?>

<!DOCTYPE html>
<html lang="eng-ENG">
  <head>
    <title>
      VintageGames
    </title>
    <meta charset="UTF-8">
     <link rel="stylesheet" href="style.css">
  </head>

  <body>

    <!--La div classe container contiene tutti gli altri elementi ad 
      eccezione del footer 
    -->
    <div class="container">

      <!-- La div classe header contiene il titolo della pagina -->
      <div class="header">
        <h1>
        VINTAGE GAMES
        </h1>
        <i> for nostalgic people </i>
      </div>

      <!-- La div classe loginArea contiene l'area di Login, generata da php -->
      <div class="loginArea">
        <?php
          require "loginArea.php";
        ?>
        <!-- La div classe links appare solo se la risoluzione è troppo bassa
             per le immagini e la tabella rankings (vedi css) -->
        <div id = "links">
          <a href="snake.php"><p> Click to play Snake! </p></a>
          <a href="Pong.php"> <p> Click to play Pong!  </p> </a> 
        </div>
      </div>

      <!-- La div rankings contiene la tabella con le classifiche, generata da php -->
      <div id="rankings">
        <h1 id="rankingTitle">
          RANKINGS
        </h1>
        <table>
          <tr>
            <th>Snake</th>
            <th>Pong</th>
          </tr>
          <?php
            //Vengono effettuate due query per ottenere i dati dei punteggi
            try { 
            require "dbConn.php";
            $sql = "SELECT * FROM users ORDER BY record_snake DESC;";
            $snakeResult = $conn -> query($sql);
            $sql = "SELECT * FROM users ORDER BY record_pong DESC;";
            $pongResult = $conn -> query($sql);
              for($i=0; $i < 10; $i++) {
                $snakeRow = $snakeResult -> fetch(PDO::FETCH_ASSOC);
                if($snakeRow != false) 
                  echo "<tr><td>".$snakeRow["username"]." -> ".$snakeRow["record_snake"]."</td>";
                else 
                  echo "<tr><td></td>";
                $pongRow = $pongResult -> fetch(PDO::FETCH_ASSOC);
                if($pongRow != false)
                  echo "<td>".$pongRow["username"]." -> ".$pongRow["record_pong"]."</td>";
                else 
                  echo "<td></td>";
                echo "</tr>";
              }
              $conn = null;
            } catch (PDOException $e) {
              echo "<tr>";
              echo "<td>Error connecting the database!</td>";
              echo "<td>Error connecting the database!</td>";
              echo "</tr>";
            }
          ?>
          <!-- L'ultima parte della tabella contiene i link ai giochi -->
          <tr>
            <th>Click to play Snake!</th>
            <th>Click to play Pong!</th>
          </tr>
          <tr>
            <td><a href="snake.php" alt="snake"><img src="Media/snake.png"></a></td>
            <td><a href="Pong.php" alt="pong"><img src="Media/pong.png"></a></td>
          </tr>
        </table>
      </div>

      <!-- La div classe description contiene una breve descrizione della pagina -->
      <div class="description">
        <p> This is a gaming blog born as a school project for the web 
          programming lesson at computer science course of Camerino University. Have fun! 
        </p>
      </div>


    </div>
    <!-- Il footer si trova sempre in fondo alla pagina html, con posizione assoluta -->
    <footer>Copyright 2016 Lorenzo Dezi, Vittorio Cipriani.</footer>

  </body>

</html>