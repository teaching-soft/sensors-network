<?php
// CODICE DA VERIFICARE
define ('SERVER_PORT','5000');
define ('DB_NAME','temperature_db');
define('DB_USER','user_temperature_db');
define('DB_PW','password_temperature_db');

// Crea il socket
if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() fallita, motivo: " . socket_strerror(socket_last_error()) . "\n";
}
// Comunica l’indirizzo su cui stare in ascolto e la porta
if (socket_bind($sock, "0.0.0.0", SERVER_PORT) === false) {
    echo "socket_bind() fallita, motivo: " . socket_strerror(socket_last_error($sock)) . "\n";
}
// Si mette in ascolto
if (socket_listen($sock, 5) === false) {
    echo "socket_listen() fallita, motivo: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
	// Arrivata richiesta
	if (($clientSocket = socket_accept($sock)) === false) {
        	echo "socket_accept() fallita, motivo: " . socket_strerror(socket_last_error($sock)) . "\n";
        	break;
    	}
	// Si legge quanto arrivato
  	if (($buf = socket_read($clientSocket, 2048, PHP_NORMAL_READ)) === false) {
            	echo "socket_read() fallita, motivo: " . socket_strerror(socket_last_error($clientSocket)) . "\n";
            	continue;
	}
	// Ottieni l'indirizzo IP del client
    	$remoteIp = '';
    	$remotePort = 0;
    	if (socket_getpeername($clientSocket, $remoteIp, $remotePort)) {
        	echo "Connessione accettata da $remoteIp:$remotePort\n";
    	} else {
        	echo "Impossibile ottenere l'indirizzo IP del client: " . socket_strerror(socket_last_error($clientSocket)) . "\n";
	}

	// Si salvano i dati nel db
	save_data_to_db($buf);
	// Si invia il messaggio così il client chiude il canale
	socket_write($clientSocket, "OK", 2);
	// Si chiude il socket figlio
	socket_close($clientSocket);
    } while (true);
// Si chiude il socket principale
socket_close($sock);

// ----------------------------------------------------------------------------
function save_data_to_db($dati_arrivati)
{
	if((strpos($dati_arrivati,"[") === false) || (strpos($dati_arrivati,"]") === false)){
echo "Formato dati arrivati non corretto, manca [ o ]:" . $dati_arrivati;
return;
}
	// Ripulisce i dati
	$buffer_tmp = str_replace ("[" , "" ,$dati_arrivati);
	$value = str_replace ("]" , "" ,$buffer_tmp);
	date_default_timezone_set('Europe/Rome');
	$data = date("Y/m/d");
	$time = date("H:i:s");
	echo $data . " " . $time . " " . $value;
	// Inserisce il dato nel db
try {
		// costruisce una stringa con l'host e il nome del database
	     	$connect_str = "mysql:host=localhost;dbname=" . DB_NAME . ";";
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
