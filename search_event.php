<?php
// Kết nối với cơ sở dữ liệu
require 'config.php'; // File chứa thông tin kết nối DB (host, user, password, database)
$conn->set_charset("utf8");

// Lấy tham số 'name' từ query string
$name = isset($_GET['name']) ? $conn->real_escape_string($_GET['name']) : '';

// Xây dựng câu truy vấn
if ($name != '') {
    $sql = "SELECT * FROM events WHERE name LIKE '%$name%' AND status = 'sắp diễn ra'";
} else {
    $sql = "SELECT * FROM events WHERE status = 'sắp diễn ra'";
}

// Thực hiện truy vấn
$result = $conn->query($sql);

// Khởi tạo mảng chứa dữ liệu sự kiện
$events = array();

if ($result && $result->num_rows > 0) {
    // Duyệt qua từng hàng và thêm vào mảng $events
    while ($row = $result->fetch_assoc()) {
        $events[] = array(
            'event_id' => (int)$row['event_id'],
            'name' => $row['name'],
            'artist_id' => (int)$row['artist_id'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'location_id' => (int)$row['location_id'],
            'event_type_id' => (int)$row['event_type_id'],
            'description' => $row['description'],
            'image_url' => $row['image_url'],
            'status' => $row['status'],
            'ticket_sale_start' => $row['ticket_sale_start'],
            'ticket_sale_end' => $row['ticket_sale_end']
        );
    }
}

// Trả về phản hồi dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($events);

// Đóng kết nối
$conn->close();
?>
