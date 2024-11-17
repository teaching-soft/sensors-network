/* giancarlomartini@gmail.com
 *
 *
 * */

#include "WiFiS3.h"
#include <string.h>

// Credenziali per la rete wifi
#include "secret.h"

// wifiClient
WiFiClient tcpClient;
int status = WL_IDLE_STATUS;

void printWifiStatus() {
/* -------------------------------------------------------------------------- */  
  // Stampa le info al termine del tentativo di connessione
  Serial.print("SSID: ");
  Serial.println(WiFi.SSID());

  // print your board's IP address:
  IPAddress ip = WiFi.localIP();
  Serial.print("IP Address: ");
  Serial.println(ip);

  // print the received signal strength:
  long rssi = WiFi.RSSI();
  Serial.print("signal strength (RSSI):");
  Serial.print(rssi);
  Serial.println(" dBm");
}
/* -------------------------------------------------------------------------- */
void setup() {
/* -------------------------------------------------------------------------- */  
  //Initialize serial and wait for port to open:
  Serial.begin(9600);
  while (!Serial) {
     // attende la connessione seriale
  }
  
  // Controlla la presenza del modulo wifi
  if (WiFi.status() == WL_NO_MODULE) {
    Serial.println("Il modulo wifi non risponde!");
    // stoppa
    while (true);
  }
  
  String fv = WiFi.firmwareVersion();
  if (fv < WIFI_FIRMWARE_LATEST_VERSION) {
    Serial.println("Controllo sulla versione del firmware fallito");
  }
  
  // prova la connessione alla rete WiFi
  while (status != WL_CONNECTED) {
    Serial.print("Provo  a connettermi al wifi, rete: ");
    Serial.println(ssid);
    status = WiFi.begin(ssid, pass);
     
    // Attende il riconoscimento
    delay(10000);
  }
  
  printWifiStatus();
 
}

void sendSensorData(){
  // Recupera i dati
  // *******************
  char sensorsData[] = "[110]";
  char response [100];
  uint8_t indexResponse = 0;
  Serial.print("Invio dati al server:");
  Serial.println(sensorsData);
  // Si connette
  if (tcpClient.connect(ipRaspberry, port)) {

    tcpClient.println(sensorsData);
    delay(100);
    while (tcpClient.available()) {
      response[indexResponse] = tcpClient.read();
      indexResponse++;
      response[indexResponse] = '\0';
      //Serial.print("RISPOSTA:");
      //Serial.println(response);
      if(strcasecmp(response,"OK") == 0){
        Serial.println("INVIO RIUSCITO");
        break;
      }
    if(strlen(response) >= 2) { 
      Serial.print("INVIO NON RIUSCITO:");
      Serial.println(response);
      break;
    }
  }
  Serial.println("Chiudo connessione");
  tcpClient.stop();
  }
}
/* -------------------------------------------------------------------------- */


/* -------------------------------------------------------------------------- */
void loop() {
  
  while (true){
    sendSensorData();
    delay(1000);
  }
}

/* -------------------------------------------------------------------------- */

