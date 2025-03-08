<?php
// Bao gồm file cấu hình để kết nối cơ sở dữ liệu
require 'config.php';

// Lấy location_id từ query string
$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : 0;

// Chuẩn bị câu lệnh SQL để lấy thông tin địa điểm dựa trên location_id
$sql = "SELECT location_id, name, address, city, capacity, description, img_url FROM locations WHERE location_id = ?";

// Chuẩn bị và thực thi câu lệnh
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $location_id);
$stmt->execute();
$result = $stmt->get_result();

// Tạo mảng để chứa dữ liệu
$locations = array();

if ($result->num_rows > 0) {
    // Lặp qua các kết quả và thêm vào mảng
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
}

// Đóng kết nối
$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($locations);
?>
