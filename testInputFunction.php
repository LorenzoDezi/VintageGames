<?php
	/*
      La funzione test_input impedisce falle nella sicurezza
        sfruttando il cross-site scripting  
      */
      function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }

?>