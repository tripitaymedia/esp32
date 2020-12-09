#include "MyServer.h"

MyServerResponse* MyServer::sendAndReceive(
     const String &name,
     const String &ip,
     int relayStatus
) {
  if (millis() < _nextStatusCheck) {
       return &resp;
  }
    _nextStatusCheck = millis() + _pingDelayMs;

     resp.relayOn = 0;
     resp.responseOk = false;

     HTTPClient httpClient;

     char url[256];
     sprintf(url, "%s?name=%s&ip=%s&relayStatus=%d", baseUrl.c_str(), name.c_str(), ip.c_str(), relayStatus);
     Serial.printf("requesting %s\n", url);

     //
     if (!httpClient.begin(url)) {
          httpClient.end();
          Serial.printf("sendAndReceive(): httpClient.begin(%s) failed\n", url);
          return &resp;
     }


     //
     //httpClient.addHeader("Host", "iot.shttps.com", true, true);
     int responseStatus = httpClient.GET();
     if (responseStatus != 200) {
          Serial.printf("sendAndReceive(): response status(%d): %s\n", responseStatus, httpClient.getString().c_str());
          httpClient.end();
          return &resp;
     }

     String result = httpClient.getString();

     Serial.printf("Received: %s\n", result.c_str());

     httpClient.end();

     resultToMyServerResponse(result);

     return &resp;
}

void MyServer::resultToMyServerResponse(const String &jsonString) {
     DynamicJsonDocument doc(1024 * 2);
     auto err = deserializeJson(doc, jsonString);
     if (err == DeserializationError::Code::Ok) {
          JsonObject obj = doc.as<JsonObject>();
          resp.relayOn  = obj[String("relayOn")];
          resp.responseOk = true;
     }
}