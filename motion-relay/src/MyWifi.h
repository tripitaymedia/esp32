#ifndef MYWIFI_H
#define MYWIFI_H

#include <WiFi.h>
#include <functional>
#include <string>

#define HASH_PRIME 9973

class WifiNetwork {
    public:
    const char* ssid;
    const char* password;
    WifiNetwork(const char * aSsid, const char * aPassword) :
    ssid(aSsid), password(aPassword) { }
};

class MyWifi {
private:
    WifiNetwork** _wifiNetworks;
    int _numNetworks;
    int curNet = 0;

    String deviceName;
    const int numTries;
    const int triesDelay;
    const int backOffDelay;
    unsigned int hash(const char *str);
    String _getDeviceName(const char * _deviceName);
    String macAddress() {
        return WiFi.macAddress();
    }

public:

    MyWifi(WifiNetwork** aWifiNetworks, int aNumNetworks, const char* a_deviceName) :
          _wifiNetworks(aWifiNetworks),
          _numNetworks(aNumNetworks),
          numTries(20),
          triesDelay(1000),
          backOffDelay(10000)
        {
            deviceName = _getDeviceName(a_deviceName);
        }

    void connect();
    String getDeviceName();


    // Print ESP32 Local IP Address
    String ip() {
        return WiFi.localIP().toString();
    }

    WifiNetwork* getNextNet();
};




#endif