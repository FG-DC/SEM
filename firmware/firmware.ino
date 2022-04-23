#include <SPIFFS.h>
#include <Arduino.h>
#include <WiFiManager.h>
#include <ArduinoMqttClient.h>
#include <driver/adc.h>
#include "EmonLib.h"

#include "config/config.h"
#include "libraries/api.cpp"
#include "libraries/utils.cpp"

//CONSTANTS
#define TIME_BETWEEN_POSTS 5000
#define TIME_BETWEEN_PUBLISHES 1000

#define ADC_BITS    12

// GLOBAL VARIABLES
WiFiServer server(80);
WiFiClient client = server.available();
MqttClient mqttClient(client);

EnergyMonitor SCT013;

char username[40] = "";
char password[40] = "";
int id;
bool shouldSaveConfig = false;
float calibrationValue = 0;
float lastMeanPower = 0;

// FUNCTIONS
float getConsumption();
void saveConfigCallback();
void readAuthFromFlashMemory();
void startWiFiManager();
void writeAuthToFlashMemory(const char* _username, const char* _password);
void connectToWiFi();
void connectToMQTTServer();
void publishMessage(String topic, float value);
void onMqttMessage(int messageSize);

void setup() {
  adc1_config_channel_atten(ADC1_CHANNEL_6, ADC_ATTEN_DB_11);
  analogReadResolution(ADC_BITS);
  Serial.begin(9600);

  readAuthFromFlashMemory();
  startWiFiManager();

  server.begin();
  connectToWiFi();
  
  connectToMQTTServer();
  mqttClient.onMessage(onMqttMessage);

  pinMode(PIN_SCT, INPUT);
  SCT013.current(PIN_SCT, 15); //calibration value

  login(client, API_ENDPOINT + "/login", username, password);
  id = getId(client, API_ENDPOINT + "/user");

  mqttClient.subscribe(String(id) + "/tare");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    if (!mqttClient.connected()) {
      connectToMQTTServer();
    }
    
    int n = 0;
	  float values[100];
	
    unsigned long postTime = millis() + TIME_BETWEEN_POSTS;
	
    while (postTime > millis()) {
      unsigned long publishTime = millis() + TIME_BETWEEN_PUBLISHES;
      
      while (publishTime > millis()) {
        values[n++] = getConsumption();
      }

      lastMeanPower = Statistic::getMean(values, n);

      //EACH 1 SECOND
      publishMessage(String(id) + "/power", lastMeanPower - calibrationValue);
    }

    //EACH 5 SECONDS
    float meanW = Statistic::getMean(values, n);
    float varianceW = Statistic::getVariance(values, n);

    Serial.print("Power: ");
    Serial.print(meanW);
    Serial.print(" W | ");
    Serial.print("Current: ");
    Serial.print(meanW / HOME_VOLTAGE, 3);
    Serial.println(" A");

    postConsumption(client, API_ENDPOINT + "/users/" + (String)id + "/consumptions", meanW, varianceW);
    Serial.println("--------------------------");
  }
}

float getConsumption() {
  //METHOD 1
  float irms = SCT013.calcIrms(1480);  //quantidade de exemplos utilizados para fazer a leitura

  /*
  //METHOD 2
  int sensorValue, sensorMax;
  uint32_t start = millis();
  while((millis() - start) < 1000) {
    sensorValue = analogRead(PIN_SCT);
    if (sensorValue > sensorMax) {
      sensorMax = sensorValue;
    }
  }
  float irms = ((sensorMax - 500) / 1024.0) * 3.3 * 0.7071 * 15.0;
  */

  float power = irms * HOME_VOLTAGE;  // Calcula o valor da Potencia Instantanea
  return power;
}

void saveConfigCallback () {
  Serial.println("Should save config");
  shouldSaveConfig = true;
}

void readAuthFromFlashMemory() {
  if (SPIFFS.begin()) {
    Serial.println("mounted file system");
    if (SPIFFS.exists("/config.json")) {
      //file exists, reading and loading
      Serial.println("reading config file");
      File configFile = SPIFFS.open("/config.json", "r");
      if (configFile) {
        Serial.println("opened config file");
        size_t size = configFile.size();
        // Allocate a buffer to store contents of the file.
        std::unique_ptr<char[]> buf(new char[size]);

        configFile.readBytes(buf.get(), size);
        DynamicJsonDocument doc(CREDENTIALS_SIZE);
        auto error = deserializeJson(doc, buf.get());
        serializeJson(doc, Serial);
        if (!error) {
          Serial.println("\nparsed json");
          strcpy(username, doc["username"]);
          strcpy(password, doc["password"]);
        } else {
          Serial.println("failed to load json config");
        }
      }
    }
  } else {
    Serial.println("failed to mount FS");
  }
}

void startWiFiManager() {
  WiFiManager wifiManager;
  //wifiManager.resetSettings();
  wifiManager.setSaveConfigCallback(saveConfigCallback);
  wifiManager.setClass("invert"); // dark theme

  WiFiManagerParameter custom_text("<p>User Credentials</p>");
  wifiManager.addParameter(&custom_text);


  WiFiManagerParameter custom_username("username", "Username", "", 40);
  wifiManager.addParameter(&custom_username);

  WiFiManagerParameter custom_password("password", "Password", "", 40);
  wifiManager.addParameter(&custom_password);

  wifiManager.autoConnect("SEM");

  if (shouldSaveConfig) {
    writeAuthToFlashMemory(custom_username.getValue(), custom_password.getValue());
  }
}

void writeAuthToFlashMemory(const char* _username, const char* _password) {
    DynamicJsonDocument doc(CREDENTIALS_SIZE);

    doc["username"] = _username;
    doc["password"] = _password;


    File configFile = SPIFFS.open("/config.json", "w");
    if (!configFile) {
      Serial.println("failed to open config file for writing");
    }

    configFile.close();

    strcpy(username, _username);
    strcpy(password, _password);
}

void connectToWiFi() {
  // Only try 15 times to connect to the WiFi
  int retries = 0;
  while (WiFi.status() != WL_CONNECTED && retries < 15){
    delay(500);
    Serial.print(".");
    retries++;
  }

  // If we still couldn't connect to the WiFi, go to deep sleep for a
  // minute and try again.
  if(WiFi.status() != WL_CONNECTED){
    esp_sleep_enable_timer_wakeup(1 * 60L * 1000000L);
    esp_deep_sleep_start();
  }

  Serial.print("\nIP: ");
  Serial.println(WiFi.localIP());
}

void connectToMQTTServer() {
  if (!mqttClient.connect(MQTT_HOST, MQTT_HOST_PORT)) {
    Serial.print("MQTT connection failed! Error code: ");
    Serial.println(mqttClient.connectError());
    return;
  }
  Serial.println("MQTT connection succeeded!");
}

void publishMessage(String topic, float value) {
  mqttClient.beginMessage(topic);
  mqttClient.print(value);
  mqttClient.endMessage();

  Serial.print("PUBLISH: \"");
  Serial.print(topic);
  Serial.print("\" -> ");
  Serial.println(value, 2);
}

/*
void onMqttMessage(int messageSize) {
  if (mqttClient.messageTopic().equals(String(id) + "/tare")) {
    
    //30 seconds read
    unsigned long startTime = millis() + 30000;
  
    while (startTime > millis()) {
      calibrationValue = lastMeanPower;
    }

    calibrationValue = 0;
  }
}
*/

void onMqttMessage(int messageSize) {
  // we received a message, print out the topic and contents
  Serial.print("Received a message with topic '");
  Serial.print(mqttClient.messageTopic());
  Serial.print("', duplicate = ");
  Serial.print(mqttClient.messageDup() ? "true" : "false");
  Serial.print(", QoS = ");
  Serial.print(mqttClient.messageQoS());
  Serial.print(", retained = ");
  Serial.print(mqttClient.messageRetain() ? "true" : "false");
  Serial.print("', length ");
  Serial.print(messageSize);
  Serial.println(" bytes:");

  // use the Stream interface to print the contents
  while (mqttClient.available()) {
    Serial.print((char)mqttClient.read());
  }
  Serial.println();

  Serial.println();
}
