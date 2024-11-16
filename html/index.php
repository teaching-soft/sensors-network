<html>
<head>
	<meta http-equiv="refresh" content="5">
	<title>PAGINA DI RECUPERO DEI DATI DEL SENSORE DI TEMPERATURA</title>
	<link rel="stylesheet" href="index.css">
</head>
<body>
	
<div class="contenitore">
<img src=header-gv.png>
</div>
<h1>PAGINA DI RECUPERO DEI DATI DEL SENSORE DI TEMPERATURA</h1>
<DIV id="content" style="text-align:center">
<TABLE class="redTable">
<CAPTION>ULTIMI 10 VALORI</CAPTION>
<TR>
<TH>ID DATO</TH><TH>DATE</TH><TH>TIME</TH><TH>TEMP °C</TH>
</TR>
<?php
   try {
    	// costruisce una stringa con l host e il nome del database
        $connect_str = "mysql:host=localhost;dbname=temperature_db;";
	// Crea una connessione con il database aggiungendo il nome utente e la password
        $db_code_handle = new PDO($connect_str,'user_temperature_db','password_temperature_db');
        // Imposta alcune opzioni (uso delle eccezioni)
        $db_code_handle->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        // Imposta la query di inserimento in una stringa aggiungendo i valori arrivati
        $query = "SELECT * FROM temperature_tbl ORDER BY id DESC LIMIT 10";
        // Esegue la query
        $result = $db_code_handle->query($query);
	// Estrae i risultati   
        while($row = $result->fetch()) //{
			echo "<TR><TD>" . $row['id'] . "</TD><TD>" . $row['date'] . "</TD><TD>" . 
				$row['time'] . "</TD><TD>" . $row['temperature'] . "</TD></TR>";
        
       
	}
    // Se qualcosa non funziona viene eseguito il codice sottostante
    catch(PDOException $e) {
    	echo "Errore:" . $e->getMessage();
        return;
   }
?>

</TABLE>
</DIV>

</body>

</html>
