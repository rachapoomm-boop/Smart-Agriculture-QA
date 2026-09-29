<?php
header("Content-Type: application/json; charset=UTF-8");

// 1. ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "Rachapoom";
$password = "NinNilnwZa007x";
$dbname = "smart_farm_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// 2. อ่านข้อมูล JSON Payload ที่ส่งมาจาก ESP32
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// 3. ตรวจสอบและบันทึกลงฐานข้อมูล
if (isset($data['temperature']) && isset($data['humidity'])) {
    $temp = floatval($data['temperature']);
    $hum = floatval($data['humidity']);

    $stmt = $conn->prepare("INSERT INTO telemetry_data (temperature, humidity) VALUES (?, ?)");
    $stmt->bind_param("dd", $temp, $hum);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Data saved successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid or missing JSON parameters"]);
}

$conn->close();
?>