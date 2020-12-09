#ifndef MYSTATES_H
#define MYSTATES_H

#include <Arduino.h>

class MyStates {
private:
    uint8_t _relayPin;
    int _relayEnabled;
    void init();

public:
    MyStates(uint8_t relayPin) :
     _relayPin(relayPin)
    {
        init();
    }

    void relayOn();
    void relayOff();
    int relayRead();
};

#endif