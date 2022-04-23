#include "api.h"

long Auth::expiresIn = 0;
char* Auth::accessToken = (char*)malloc(2048 * sizeof(char));
char* Auth::refreshToken = (char*)malloc(1024 * sizeof(char));

void login(WiFiClient client, String endpoint, const char* username, const char* password) {
	
  Serial.print("LOGIN: ");
  HTTPClient http;
  http.begin(client, endpoint);
  http.addHeader("Content-Type", "application/json");

  //REQUEST
  DynamicJsonDocument requestDoc(CREDENTIALS_SIZE);
  requestDoc["username"] = username;
  requestDoc["password"] = password;

  char requestPtr[CREDENTIALS_SIZE];

  serializeJson(requestDoc, requestPtr);
  int responseCode = http.POST(requestPtr);

  String payload;
  if (responseCode > 0) {
	payload = http.getString();

	DynamicJsonDocument responseDoc(AUTH_SIZE);

	auto error = deserializeJson(responseDoc, payload);
	if (error) {
	  Serial.print("Error - Deserializing JSON - ");
	  Serial.print(error.c_str());
	} else {
	  Auth::expiresIn = (long)responseDoc["expires_in"];
	  strcpy(Auth::accessToken, responseDoc["access_token"]);
	  strcpy(Auth::refreshToken, responseDoc["refresh_token"]);
	  Serial.println("OK");
	}
  }
  else {
	Serial.println("Error");
  }
  http.end();
}

void refresh(WiFiClient client, String endpoint, const char* token) {
	
  Serial.print("REFRESH: ");

  HTTPClient http;
  http.begin(client, endpoint);
  http.addHeader("Content-Type", "application/json");

  DynamicJsonDocument requestDoc(AUTH_SIZE);
  requestDoc["refresh_token"] = token;

  char requestPtr[AUTH_SIZE];
  serializeJson(requestDoc, requestPtr);
  int responseCode = http.POST(requestPtr);

  if (responseCode > 0) {
	String payload = http.getString();

	DynamicJsonDocument responseDoc(AUTH_SIZE);
	auto error = deserializeJson(responseDoc, payload);

	if (error) {
	  Serial.println("Error - Deserializing JSON");
	} else {
	  Auth::expiresIn = (long)responseDoc["expired_in"];
	  strcpy(Auth::accessToken, responseDoc["access_token"]);
	  strcpy(Auth::refreshToken, responseDoc["refresh_token"]);

	  Serial.println("OK");
	}
  }
  else {
	Serial.println("Error");
  }
  http.end();
}

void postConsumption(WiFiClient client, String endpoint, float value, float variance) {
	
  Serial.print("POST CONSUMPTION: ");
  HTTPClient http;
  http.begin(client, endpoint);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Authorization", "Bearer " + (String)Auth::accessToken);

  DynamicJsonDocument requestDoc(POST_SIZE);
  requestDoc["value"] = value;
  requestDoc["variance"] = variance;

  char requestPtr[POST_SIZE];
  serializeJson(requestDoc, requestPtr);

  int responseCode = http.POST(requestPtr);
  if (responseCode > 0) {
	Serial.println("OK");
  }
  else {
	Serial.println("Error");
  }
  http.end();
}

int getId(WiFiClient client, String endpoint) {
  HTTPClient http;
  http.begin(client, endpoint);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Authorization", "Bearer " + (String)Auth::accessToken);
  int responseCode = http.GET();

  if (responseCode > 0) {
	String payload = http.getString();

	DynamicJsonDocument responseDoc(USER_SIZE);

	auto error = deserializeJson(responseDoc, payload);
	if (error) {
	  Serial.print("Error - Deserializing JSON - ");
	  Serial.print(error.c_str());
	}
	else {
	  return (int)responseDoc["id"];
	}
  }

  http.end();
  return -1;
}
