
build: src/*.cpp
	platformio run

upload: build
	platformio run --target upload

monitor:
	platformio device monitor -b 115200
