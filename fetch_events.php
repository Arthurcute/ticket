<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include 'config.php';

$sql = "SELECT name, start_time, status FROM events";
$result = $conn->query($sql);

$events = array();

if ($result->num_rows > 0) {
    // Lưu dữ liệu vào mảng
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($events);

$conn->close();
?>