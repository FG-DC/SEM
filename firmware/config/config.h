#ifndef _CONFIG_
#define _CONFIG_

// SENSOR SCT-013
#define PIN_SCT 34

// HOME VOLTAGE (230v EU, 120v USA)
#define HOME_VOLTAGE 230


const String API_ENDPOINT = "http://192.168.1.66:8000/api";

const char MQTT_HOST[] = "192.168.1.98";
int MQTT_HOST_PORT = 1883;

#endif