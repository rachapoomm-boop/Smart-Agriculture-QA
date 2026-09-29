import cv2
import requests
import os
import glob
import time
from ultralytics import YOLO


# 1. โหลดโมเดล YOLO
model_path = 'weights/best.pt' if os.path.exists('weights/best.pt') else 'yolov8n.pt'
print(f"กำลังใช้งานโมเดล: {model_path}")
model = YOLO(model_path)

# 2. ตั้งค่า API URL
API_URL = "http://localhost:8080/lab_10/smart_farm/api/store_inventory.php"

# 3. ค้นหารูปภาพทั้งหมดในโฟลเดอร์ datasets
search_pattern = os.path.join('datasets', '**', '*.jpg')
image_files = glob.glob(search_pattern, recursive=True) + glob.glob(os.path.join('datasets', '**', '*.png'), recursive=True)

if not image_files:
    print("ไม่พบไฟล์รูปภาพในโฟลเดอร์ datasets")
else:
    print(f"พบรูปภาพทั้งหมด {len(image_files)} รูป กำลังเริ่มประมวลผลวนลูปตลอดเวลา...")

    # 🔄 วนลูปทำงานไปเรื่อยๆ (กด Ctrl + C ใน Terminal เมื่อต้องการหยุด)
    while True:
        for idx, img_path in enumerate(image_files, start=1):
            print(f"\n==========================================")
            print(f"[{idx}/{len(image_files)}] กำลังประมวลผล: {img_path}")
            print(f"==========================================")
            
            frame = cv2.imread(img_path)
            if frame is None:
                continue

            results = model(frame)
            counts = {'apple': 0, 'mango': 0, 'orange': 0}

            for r in results:
                for box in r.boxes:
                    cls_id = int(box.cls[0])
                    class_name = model.names[cls_id]
                    if class_name in counts:
                        counts[class_name] += 1

            print("จำนวนผลไม้ที่ตรวจจับได้:", counts)

            # ส่งข้อมูลเข้า API
            payload = {
                'apple': counts['apple'],
                'mango': counts['mango'],
                'orange': counts['orange']
            }

            try:
                res = requests.post(API_URL, json=payload, timeout=3)
                print("API Response:", res.json())
            except Exception as e:
                print("Error sending inventory data:", e)

            # หน่วงเวลา 2 วินาทีก่อนส่งรูปถัดไป เพื่อให้เห็นผลบน Flutter Dashboard
            time.sleep(2)