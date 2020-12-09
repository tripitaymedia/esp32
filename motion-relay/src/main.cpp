#include <Arduino.h>

#include "MyWifi.h"
#include "MyServer.h"
#include "MyStates.h"

#define RELAY_PIN 16

#define PING_RESPONSE_CACHE_MS 1000
#define LOOP_DELAY_MS 500

#define BOUD_RATE 9600

// WiFi
WifiNetwork net1("SSID-1", "SSID-1-PASSWORD");
WifiNetwork net2("SSID-2", "SSID-2-PASSWORD");
WifiNetwork* networks[] = {&net1, &net2};

// Pin Management
MyStates states(RELAY_PIN);
MyStates* statesPtr  = &states;


MyWifi wifi(networks, 1, "esp32-%u");
MyServer myserver("http://iot.shttps.com/iot.php", wifi.getDeviceName(), PING_RESPONSE_CACHE_MS);
MyServerResponse* httpResponse = 0;

void setup(){
  states.relayOff();
  Serial.begin(BOUD_RATE);
  wifi.connect();
  Serial.println(wifi.ip()); // Print ESP32 Local IP Address
}

void loop(){

  // Only reports every PING_RESPONSE_CACHE_MS
  httpResponse = myserver.sendAndReceive(
    wifi.getDeviceName(),
    wifi.ip(),
    states.relayRead()
  );

if (httpResponse->responseOk && httpResponse->relayOn == 1) {
  states.relayOn();
} else {
  states.relayOff();
}
  delay(LOOP_DELAY_MS);
}
