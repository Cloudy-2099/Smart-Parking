#include <MQTT.h>

#include <ESP8266WiFi.h>

// Konfigurasi WiFi
const char* ssid = "bengkel las SB";         // Ganti dengan SSID WiFi Anda
const char* password = "jangannumpang!"; // Ganti dengan password WiFi Anda

// Konfigurasi MQTT
const char* mqttServer = "broker.emqx.io"; // Ganti dengan alamat broker MQTT Anda
const int mqttPort = 1883;                   // Port MQTT

const char* mqttPassword = "YOUR_MQTT_PASSWORD"; // Ganti jika broker Anda membutuhkan password

WiFiClient net;
MQTTClient client(512); // MQTTClient dengan buffer ukuran 512 byte

// Pin untuk 4 sensor infrared
const int sensor1Pin = D5;
const int sensor2Pin = D6;
const int sensor3Pin = D7;
const int sensor4Pin = D8;

// Variabel untuk menyimpan status sebelumnya
int prevSensor1Value = HIGH;
int prevSensor2Value = HIGH;
int prevSensor3Value = HIGH;
int prevSensor4Value = HIGH;

void connectWiFi() {
  Serial.print("Menghubungkan ke WiFi");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nTerhubung ke WiFi!");
}

void connectMQTT() {
  Serial.print("Menghubungkan ke broker MQTT...");
  while (!client.connect("CamellyaMyWifey")) {
    Serial.print(".");
    delay(2000);
  }
  Serial.println("\nTerhubung ke broker MQTT!");
}

void setup() {
  // Inisialisasi Serial Monitor
  Serial.begin(9600);

  // Inisialisasi pin sensor
  pinMode(sensor1Pin, INPUT);
  pinMode(sensor2Pin, INPUT);
  pinMode(sensor3Pin, INPUT);
  pinMode(sensor4Pin, INPUT);

  // Hubungkan ke WiFi
  connectWiFi();

  // Konfigurasi MQTT
  client.begin(mqttServer, mqttPort, net);

  // Hubungkan ke broker MQTT
  connectMQTT();

  Serial.println("Sistem deteksi objek dengan MQTT siap...");
}

void loop() {
  // Pastikan koneksi MQTT tetap aktif
  if (!client.connected()) {
    connectMQTT();
  }
  client.loop();

  // Membaca status dari masing-masing sensor
  int sensor1Value = digitalRead(sensor1Pin);
  int sensor2Value = digitalRead(sensor2Pin);
  int sensor3Value = digitalRead(sensor3Pin);
  int sensor4Value = digitalRead(sensor4Pin);

  // Mengecek perubahan status untuk Sensor 1
  if (sensor1Value != prevSensor1Value) {
    String message = sensor1Value == LOW ? "detected" : "not_detected";
    client.publish("sensor/1", message);
    Serial.println("Sensor 1: " + message);
    prevSensor1Value = sensor1Value;
  }

  // Mengecek perubahan status untuk Sensor 2
  if (sensor2Value != prevSensor2Value) {
    String message = sensor2Value == LOW ? "detected" : "not_detected";
    client.publish("sensor/2", message);
    Serial.println("Sensor 2: " + message);
    prevSensor2Value = sensor2Value;
  }

  // Mengecek perubahan status untuk Sensor 3
  if (sensor3Value != prevSensor3Value) {
    String message = sensor3Value == LOW ? "detected" : "not_detected";
    client.publish("sensor/3", message);
    Serial.println("Sensor 3: " + message);
    prevSensor3Value = sensor3Value;
  }

  // Mengecek perubahan status untuk Sensor 4
  if (sensor4Value != prevSensor4Value) {
    String message = sensor4Value == LOW ? "detected" : "not_detected";
    client.publish("sensor/4", message);
    Serial.println("Sensor 4: " + message);
    prevSensor4Value = sensor4Value;
  }

  delay(100); // Delay untuk menghindari pembacaan terlalu cepat
}
