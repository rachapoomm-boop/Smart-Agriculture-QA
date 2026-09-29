#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "DHT.h"

// --- ตั้งค่า Hardware ---
#define DHTPIN 5          // ขาสัญญาณ Data ของ DHT (หากค่ายังเพี้ยนลองย้ายไปใช้ GPIO 5)
#define DHTTYPE DHT11     // หากเป็นเซนเซอร์ตัวสีขาวให้เปลี่ยนเป็น DHT22

// --- ตั้งค่า Network & Server ---
const char* ssid = "N"; 
const char* password = "223344550"; 
const char* serverUrl = "http://10.113.153.145:8080/lab_10/smart_farm/api/store_telemetry.php";

DHT dht(DHTPIN, DHTTYPE);

void setup() {
    Serial.begin(115200);
    delay(1000);
    
    // เริ่มต้นเซนเซอร์ DHT
    dht.begin();
    delay(2000); // หน่วงเวลา 2 วินาทีให้วงจรเซนเซอร์อุ่นเครื่องพร้อมทำงาน
    
    Serial.println();
    Serial.print("Connecting to WiFi: ");
    Serial.println(ssid);
    
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nConnected to WiFi successfully!");
    Serial.print("ESP32 IP Address: ");
    Serial.println(WiFi.localIP());
}

void loop() {
    if (WiFi.status() == WL_CONNECTED) {
        // อ่านค่าอุณหภูมิและความชื้น
        float h = dht.readHumidity();
        float t = dht.readTemperature();

        // ตรวจสอบว่าอ่านค่าสำเร็จหรือไม่
        if (!isnan(h) && !isnan(t)) {
            Serial.print("Temp: ");
            Serial.print(t);
            Serial.print(" C, Hum: ");
            Serial.print(h);
            Serial.println(" %");

            HTTPClient http;
            http.begin(serverUrl);
            http.addHeader("Content-Type", "application/json");

            // สร้างโครงสร้างข้อมูล JSON
            JsonDocument doc;
            doc["temperature"] = t;
            doc["humidity"] = h;

            String jsonPayload;
            serializeJson(doc, jsonPayload);

            // ส่ง HTTP POST ไปยังเซิร์ฟเวอร์
            int httpResponseCode = http.POST(jsonPayload);
            
            if (httpResponseCode > 0) {
                String response = http.getString();
                Serial.print("HTTP Response Code: ");
                Serial.println(httpResponseCode);
                Serial.print("Server Output: ");
                Serial.println(response);
            } else {
                Serial.print("HTTP Error code: ");
                Serial.println(httpResponseCode);
            }
            http.end();
        } else {
            Serial.println("Failed to read from DHT sensor! Check wiring.");
        }
    } else {
        Serial.println("WiFi Disconnected!");
    }
    
    delay(10000); // อ่านและส่งข้อมูลทุกๆ 10 วินาที
}