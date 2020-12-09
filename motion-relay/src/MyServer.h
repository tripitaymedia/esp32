#ifndef MYSERVER_H
#define MYSERVER_H

#include <Arduino.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

class MyServerResponse {
public:
    int relayOn;
    String script;
    bool responseOk;
    MyServerResponse(int aRelayOn, String aScript, bool aResponseOk)
        : relayOn(aRelayOn),
          script(aScript),
          responseOk(aResponseOk) {}

    MyServerResponse() {
        relayOn = 0;
        script = "";
        responseOk = false;
    }
    // Copy Constructor
    MyServerResponse(const MyServerResponse &old) {
        relayOn = old.relayOn;
        script = old.script;
        responseOk = old.responseOk;
    }
};

class MyServer {
private:
    String baseUrl;
    String deviceName;
    MyServerResponse resp;
    unsigned long _pingDelayMs;
    unsigned long _nextStatusCheck;
    void resultToMyServerResponse(const String &jsonString);

public:
    MyServer(String baseUrlA, String aDeviceName, unsigned long aPingDelayMs)
     : baseUrl(baseUrlA),
       deviceName(aDeviceName),
       _pingDelayMs(aPingDelayMs)
    {
        _nextStatusCheck = millis() + _pingDelayMs;
    }
    MyServerResponse* sendAndReceive(const String &name, const String &ip, int relayStatus);
};

String encodeStatuses(int relayStatus, int pirStatus);
MyServerResponse resultToMyServerResponse(const String &result);

#endif