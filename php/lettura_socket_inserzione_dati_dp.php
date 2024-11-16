<?php
// CODICE DA VERIFICARE
define ('SERVER_PORT','5000');
define (‘DB_NAME’,’temperature_db’);
define(‘DB_USER’,'user_temperature_db');
define(‘DB_PW’,'password_temperature_db');
define(‘MSG_TO_SEND’,'BYEBYE');

// Crea il socket	
if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() fallita, motivo: " . socket_strerror(socket_last_error()) . "\n";
}
// Comunica l’indirizzo su cui stare in ascolto e la porta
if (socket_bind($sock, ‘0.0.0.0’, SERVER_PORT) === false) {
    echo "socket_bind() fallita, motivo: " . socket_strerror(socket_last_error($sock)) . "\n";
}
// Si mette in ascolto
if (socket_listen($sock, 5) === false) {
    echo "socket_listen() fallita, motivo: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
	// Arrivata richiesta
if (($msgsock = socket_accept($sock)) === false) {
        		echo "socket_accept() fallita, motivo: " . socket_strerror(socket_last_error($sock)) . "\n";
        		break;
    	}
	// Si legge quanto arrivato
        	if ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ)) === false) {
            	echo "socket_read() fallita, motivo: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            	continue;
}
// Si salvano i dati nel db
save_data_to_db($buf);
	// Si invia il messaggio così il client chiude il canale
        	socket_write($msgsock, MSG_TO_SEND, strlen(MSG_TO_SEND));
	// Si chiude il socket figlio
socket_close($msgsock);
    } while (true);
// Si chiude il socket principale    
socket_close($sock);

// ----------------------------------------------------------------------------
function save_data_to_db($dati_arrivati)
{
	if((strpos($buffer,"[") === false) || (strpos($buffer,"]") === false)){
echo “Formato dati arrivati non corretto, manca [ o ]:” . $dati_arrivati;
return;
}
	// Ripulisce i dati
	$buffer_tmp = str_replace ("[" , "" ,$dati_arrivati);
$values = str_replace ("]" , "" ,$buffer_tmp);
// echo $values . "\n";
// Spacchetta i dati data, ora, valore
	$fields = explode(‘,’,values);
	$data = $fields[0];
	$time = $fields[1];
	$value = $fields[2];
echo $data . " " . $time . " " . $value;
	// Inserisce il dato nel db
try {
		// costruisce una stringa con l'host e il nome del database
	     	$connect_str = "mysql:host=localhost;dbname=” . DB_NAME . “;";
	           // Crea una connessione con il database aggiungendo il nome utente e la password
		$db_handle = new PDO($connect_str,DB_USER,DB_PW);
	           // Imposta alcune opzioni (uso delle eccezioni)
	           $db_handle->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	           // Imposta la query di inserimento in una stringa aggiungendo i valori arrivati
	           $query = "INSERT INTO temperature_tbl (date, time,temperature) VALUES ('" . $data . "','" . $time . "','" . $value . "')";
	           // Comunica al server db di preparare la query
	           $sth = $db_handle->prepare($query);
	            // Gli dice di seguirla
	                $result = $sth->execute();
	       }
	      // Se qualcosa non funziona viene eseguito il codice sottostante
	      catch(PDOException $e) {
	      		echo "Errore:" . $e->getMessage();
	      		return;
	      }
	      echo "Dati inseriti!: " . $data . " " . $time . " " . $value . "\n";
}
?>

