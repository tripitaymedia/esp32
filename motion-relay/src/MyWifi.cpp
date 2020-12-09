#include "MyWifi.h"

unsigned int MyWifi::hash(const char *str) {
        unsigned int sum = 0;;
        for (int c = *str; c != 0; str++, c=*str) {
           sum += c;
        }
        return sum % HASH_PRIME;
}

String MyWifi::_getDeviceName(const char * _deviceName) {
    char buff[256];
    unsigned int h  = hash(macAddress().c_str());
    sprintf(buff, _deviceName, h);
    String res(buff);
    return res;
}

String MyWifi::getDeviceName() {
  return deviceName;
}

void MyWifi::connect() {
  // Connect to Wi-Fi
  while (WiFi.status() != WL_CONNECTED) {
    WifiNetwork* network = getNextNet();
    WiFi.mode(WIFI_STA);
    WiFi.disconnect();
    WiFi.begin(network->ssid, network->password);
    WiFi.setHostname(getDeviceName().c_str());
    Serial.printf("Connecting to WiFi %s\n", network->ssid);
    for (int i = 0; i < numTries && (WiFi.status() != WL_CONNECTED); i++) {
      Serial.print(".");
      delay(triesDelay);
    }
    Serial.println("");

    if (WiFi.status() == WL_CONNECTED) {
      Serial.printf("Connected to %s\n", network->ssid);
    } else {
      Serial.println("Not Connected... sleep and try again");
      delay(backOffDelay);
    }
  }
}

WifiNetwork* MyWifi::getNextNet() {
  if (curNet >= _numNetworks) { curNet = 0; }
  WifiNetwork * nextNet = _wifiNetworks[curNet];
  Serial.printf("\tnext net .. [%d] : %s\n", curNet, nextNet->ssid);
  curNet++;
  return nextNet;
}