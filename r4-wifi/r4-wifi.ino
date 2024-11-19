/* giancarlomartini@gmail.com
 *
 *
 * */

#include "WiFiS3.h"
#include <string.h>

// Credenziali per la rete wifi
#include "secret.h"

// Costanti da modificare
#define PIN_ADC A0
#define GRUPPO_ID 1


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

void sendSensorData(char *dataToSend){
  char response [100];
  uint8_t indexResponse = 0;
  Serial.print("Invio dati al server:");
  Serial.println(dataToSend);
  // Si connette
  if (tcpClient.connect(ipRaspberry, port)) {

    tcpClient.println(dataToSend);
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
     // Recupero temperatura
    float lettura_adc = analogRead(PIN_ADC);
    float milli_volt = (lettura_adc * 5000) / 1024.0;
    float temperatura = milli_volt / 10.0;
    char dataToSend[50];
    sprintf(dataToSend,"[G%d:%.1f]",GRUPPO_ID,temperatura);
    sendSensorData(dataToSend);
    delay(1000);
  }
}

/* -------------------------------------------------------------------------- */

