<?php
header("Content-Type: application/json; charset=UTF-8");

// ปิดการพ่น Error หน้าจอเพื่อป้องกัน JSON พัง
ini_set('display_errors', 0);
error_reporting(E_ALL);

$servername = "localhost";
$username = "Rachapoom";
$password = "NinNilnwZa007x";
$dbname = "smart_farm_db";

try {
    // กำหนดให้ MySQLi แจ้งเตือนข้อผิดพลาดผ่าน Exception
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($servername, $username, $password, $dbname);

    // รับค่า JSON Payload จาก Python
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Invalid or empty JSON payload"]);
        exit();
    }

    $fruits = ['apple', 'mango', 'orange'];
    $inserted_count = 0;

    foreach ($fruits as $fruit) {
        if (isset($data[$fruit]) && $data[$fruit] > 0) {
            $qty = intval($data[$fruit]);
            $stmt = $conn->prepare("INSERT INTO warehouse_inventory (item_name, quantity, unit) VALUES (?, ?, 'ลูก')");
            $stmt->bind_param("si", $fruit, $qty);
            $stmt->execute();
            $stmt->close();
            $inserted_count++;
        }
    }

    echo json_encode([
        "status" => "success",
        "message" => "YOLO Fruit inventory stored successfully",
        "inserted_items" => $inserted_count
    ]);

    $conn->close();

} catch (Exception $e) {
    // หากเกิดข้อผิดพลาด ให้ส่งรายละเอียด Error กลับมาเป็น JSON
    echo json_encode([
        "status" => "db_error",
        "message" => $e->getMessage()
    ]);
}
?>