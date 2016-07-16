<?php
/*
	Questo script php è utilizzato per la connessione al database 
*/
$servername = "mysql:host=localhost; dbname=game_blog";
$user = "root";
$pwd = "";
// Crea la connessione. Utilizzando i php Data Object,
// posso gestire gli errori con l'utilizzo del costrutto try
// catch. dbConn.php, quando importato, deve essere racchiuso
// insieme al codice seguente da tale costrutto
$conn = new PDO($servername, $user, $pwd);

?>