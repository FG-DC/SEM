#ifndef _API_
#define _API_

#include <ArduinoJson.h>
#include <HTTPClient.h>

#define CREDENTIALS_SIZE 100
#define AUTH_SIZE 1900
#define POST_SIZE 500
#define USER_SIZE 500

struct Auth {
  static long expiresIn;
  static char *accessToken;
  static char *refreshToken;
};

// @POST /login
void login(WiFiClient client, String endpoint, const char* username, const char* password);

// @POST /refresh
void refresh(WiFiClient client, String endpoint, const char* token);

// @POST /users/{id}/consumptions
void postConsumptions(WiFiClient client, String endpoint, float *values, float *variances, long *timestamps, const int size);

// @GET /user
int getId(WiFiClient client, String endpoint);

#endif