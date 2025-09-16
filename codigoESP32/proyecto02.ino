#include <DHT.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>

// WiFi Configuration
const char* ssid = "Tenda_CD9840";
const char* password = "D7KnYjjq";

// Server URL
const char* serverUrl = "https://riego.onrender.com";

// DHT11 Sensor Configuration
#define DHTPIN 25        
#define DHTTYPE DHT11   
DHT dht(DHTPIN, DHTTYPE);

// Soil Moisture Sensor Pins (5 total)
const int soilMoisturePins[] = {34, 35, 32, 33, 36}; // 5 different pins
const int NUMERO_SENSORES = 5;

// Relay Pins (with inverse logic)
const int relayActivar = 19;    
const int relayDesactivar = 18; 

// Control Variables
bool bombaActiva = false;
unsigned long tiempoInicioBomba = 0;
const unsigned long TIEMPO_RIEGO = 600000; // 10 minutes
const int HUMEDAD_MINIMA = 40;

// Send Interval (now 5 seconds)
const unsigned long INTERVALO_ENVIO = 5000; // 5 seconds
unsigned long ultimoEnvio = 0;

void activarBomba() {
  if (!bombaActiva) {
    digitalWrite(relayActivar, LOW); // Inverse logic: LOW activates
    delay(500);
    digitalWrite(relayActivar, HIGH);
    bombaActiva = true;
    tiempoInicioBomba = millis();
    enviarEstadoBomba("ACTIVADA");
  }
}

void desactivarBomba() {
  if (bombaActiva) {
    digitalWrite(relayDesactivar, LOW); // Inverse logic: LOW deactivates
    delay(500);
    digitalWrite(relayDesactivar, HIGH);
    bombaActiva = false;
    enviarEstadoBomba("DESACTIVADA");
  }
}

// Function to calculate average soil moisture
int calcularPromedioHumedadSuelo() {
  long sumaHumedad = 0;
  
  for (int i = 0; i < NUMERO_SENSORES; i++) {
    int lecturaRaw = analogRead(soilMoisturePins[i]);
    int humedadPorcentaje = map(lecturaRaw, 4095, 0, 0, 100);
    sumaHumedad += humedadPorcentaje;
  }
  
  return sumaHumedad / NUMERO_SENSORES;
}

void setup() {
  Serial.begin(115200);
  
  // Connect to WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
  
  // Initialize soil moisture sensors
  for (int i = 0; i < NUMERO_SENSORES; i++) {
    pinMode(soilMoisturePins[i], INPUT);
  }
  
  dht.begin();
  
  // Set relay pins with inverse logic (HIGH = off)
  pinMode(relayActivar, OUTPUT);
  pinMode(relayDesactivar, OUTPUT);
  digitalWrite(relayActivar, HIGH);
  digitalWrite(relayDesactivar, HIGH);
}

void enviarDatos(float temperatura, int humedadSuelo) {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    HTTPClient https;
    
    // Disable certificate validation (not recommended for production)
    client.setInsecure();
    
    String url = String(serverUrl) + "/datos.php";
    https.begin(client, url);
    https.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
    String datos = "temperatura=" + String(temperatura) + 
                  "&humedad_suelo=" + String(humedadSuelo) + 
                  "&bomba_activa=" + String(bombaActiva);
    
    int httpResponseCode = https.POST(datos);
    
    if (httpResponseCode > 0) {
      String response = https.getString();
      Serial.println("Datos enviados: " + response);
    } else {
      Serial.println("Error enviando datos");
    }
    
    https.end();
  }
}

void enviarEstadoBomba(String estado) {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    HTTPClient https;
    
    client.setInsecure();
    
    String url = String(serverUrl) + "/estado_bomba.php";
    https.begin(client, url);
    https.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
    https.POST("estado_bomba=" + estado);
    https.end();
  }
}

void verificarComandosWeb() {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    HTTPClient https;
    
    client.setInsecure();
    
    String url = String(serverUrl) + "/obtener_comando.php";
    https.begin(client, url);
    int httpCode = https.GET();
    
    if (httpCode > 0) {
      String comando = https.getString();
      if (comando == "ACTIVAR") {
        activarBomba();
      } else if (comando == "DESACTIVAR") {
        desactivarBomba();
      }
    }
    
    https.end();
  }
}

void loop() {
  // Read sensors
  int humedadSuelo = calcularPromedioHumedadSuelo();
  float temperatura = dht.readTemperature();
  
  // Periodic data sending (now every 5 seconds)
  if (millis() - ultimoEnvio >= INTERVALO_ENVIO) {
    enviarDatos(temperatura, humedadSuelo);
    ultimoEnvio = millis();
  }
  
  // Automatic control
  if (humedadSuelo < HUMEDAD_MINIMA && !bombaActiva) {
    activarBomba();
  }
  
  // Check irrigation time
  if (bombaActiva && (millis() - tiempoInicioBomba >= TIEMPO_RIEGO)) {
    desactivarBomba();
  }
  
  // Check web commands
  verificarComandosWeb();
  
  delay(1000);
}