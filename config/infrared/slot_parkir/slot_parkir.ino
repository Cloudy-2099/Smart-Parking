// Pin untuk 4 sensor infrared
const int sensor1Pin = D5; // Sensor 1
const int sensor2Pin = D6; // Sensor 2
const int sensor3Pin = D7; // Sensor 3
const int sensor4Pin = D8; // Sensor 4

// Variabel untuk menyimpan status sebelumnya
int prevSensor1Value = HIGH;
int prevSensor2Value = HIGH;
int prevSensor3Value = HIGH;
int prevSensor4Value = HIGH;

void setup() {
  // Inisialisasi Serial Monitor
  Serial.begin(115200);

  // Konfigurasi pin sensor sebagai input
  pinMode(sensor1Pin, INPUT);
  pinMode(sensor2Pin, INPUT);
  pinMode(sensor3Pin, INPUT);
  pinMode(sensor4Pin, INPUT);

  Serial.println("Sistem deteksi objek dengan perubahan status siap...");
}

void loop() {
  // Membaca status dari masing-masing sensor
  int sensor1Value = digitalRead(sensor1Pin);
  int sensor2Value = digitalRead(sensor2Pin);
  int sensor3Value = digitalRead(sensor3Pin);
  int sensor4Value = digitalRead(sensor4Pin);

  // Mengecek perubahan status untuk Sensor 1
  if (sensor1Value != prevSensor1Value) {
    Serial.print("Sensor 1: ");
    Serial.println(sensor1Value == LOW ? "Objek terdeteksi!" : "Tidak ada objek.");
    prevSensor1Value = sensor1Value; // Perbarui status sebelumnya
  }

  // Mengecek perubahan status untuk Sensor 2
  if (sensor2Value != prevSensor2Value) {
    Serial.print("Sensor 2: ");
    Serial.println(sensor2Value == LOW ? "Objek terdeteksi!" : "Tidak ada objek.");
    prevSensor2Value = sensor2Value; // Perbarui status sebelumnya
  }

  // Mengecek perubahan status untuk Sensor 3
  if (sensor3Value != prevSensor3Value) {
    Serial.print("Sensor 3: ");
    Serial.println(sensor3Value == LOW ? "Objek terdeteksi!" : "Tidak ada objek.");
    prevSensor3Value = sensor3Value; // Perbarui status sebelumnya
  }

  // Mengecek perubahan status untuk Sensor 4
  if (sensor4Value != prevSensor4Value) {
    Serial.print("Sensor 4: ");
    Serial.println(sensor4Value == LOW ? "Objek terdeteksi!" : "Tidak ada objek.");
    prevSensor4Value = sensor4Value; // Perbarui status sebelumnya
  }

  delay(100); // Delay untuk menghindari pembacaan terlalu cepat
}
