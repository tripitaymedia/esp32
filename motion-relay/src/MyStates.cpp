#include "MyStates.h"

void MyStates::init() {
    // init Relay PIN
    pinMode(_relayPin, OUTPUT);
    digitalWrite(_relayPin, LOW);
}

void MyStates::relayOn() {
  if (_relayEnabled == 0) {
    Serial.printf("relayOn() ... writing HIGH on GPIO(%d)\n", _relayPin);
    _relayEnabled = 1;
    digitalWrite(_relayPin, HIGH);
  }
}

void MyStates::relayOff() {
  if (_relayEnabled == 1) {
    Serial.printf("relayOff() ... writing LOW on GPIO(%d)\n", _relayPin);
    _relayEnabled = 0;
    digitalWrite(_relayPin, LOW);
  }
}

/**
 */
int MyStates::relayRead() {
  return digitalRead(_relayPin);
}
