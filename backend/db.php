<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
// ... โค้ดเดิมที่เหลือ ...
$host = "localhost";
$user = "Rachapoom";
$pass = "NinNilnwZa007x";
$dbname = "smart_farm_db";
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database Connection Failed"]));
}
?>