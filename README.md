# 🌾 Smart Agriculture QA System

ระบบตรวจจับคุณภาพและติดตามผลผลิตทางการเกษตรอัจฉริยะแบบ End-to-End (Smart Farm Monitoring) ที่รวมการตรวจจับนับจำนวนผลไม้ด้วย **YOLOv8**, จัดเก็บข้อมูลผ่าน **PHP/MySQL REST API**, และแสดงผล Real-time บน **Flutter Mobile Application**

---

## 📁 โครงสร้างโปรเจกต์ (Repository Structure)

```text
Smart-Agriculture-QA/
├── hardware/           # โค้ด ESP32 (.ino) สำหรับอ่านค่าเซนเซอร์
├── ai_engine/          # โค้ด YOLOv8 + Python Client (test_yolo_img.py, best.pt)
├── backend/            # PHP REST APIs + ไฟล์สำรองฐานข้อมูล MySQL (smart_farm_db.sql)
├── mobile_app/         # โปรเจกต์ Flutter สำหรับแอปพลิเคชัน Dashboard
├── .gitignore          # ไฟล์ระบุข้อยกเว้นการ Push ขึ้น Git
└── README.md           # เอกสารคู่มืออธิบายการติดตั้งและรันระบบ