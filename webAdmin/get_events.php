<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Tùy theo cấu hình của bạn
$password = ""; // Tùy theo cấu hình của bạn
$dbname = "quanlysukien"; // Tên cơ sở dữ liệu của bạn

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ bảng events
$sql = "SELECT event_id, name, status, start_time FROM events";
$result = $conn->query($sql);

// Tạo mảng để lưu trữ sự kiện
$events = array();

// Kiểm tra và lấy dữ liệu
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row; // Thêm dữ liệu vào mảng
    }
}

// Trả về kết quả dạng JSON
header('Content-Type: application/json; charset=utf-8'); // Đặt tiêu đề JSON
echo json_encode($events, JSON_UNESCAPED_UNICODE); // Sử dụng JSON_UNESCAPED_UNICODE để đảm bảo hỗ trợ tiếng Việt

// Đóng kết nối
$conn->close();
?>
