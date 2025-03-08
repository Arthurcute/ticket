<?php
require 'config.php';

// Thiết lập charset
$conn->set_charset("utf8");

// Chuẩn bị câu lệnh SQL
//$sql = "INSERT INTO tickets (ticket_type_id, seat_number, status) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Khởi tạo giá trị cho biến
$ticket_type_id = 3;
$status = 'chưa bán';

// Chèn 100 hàng vào bảng
for ($i = 1; $i <= 200; $i++) {
    $seat_number = 'CHONTIM-B' . $i;
    
    // Liên kết tham số và thực thi câu lệnh
    $stmt->bind_param("iss", $ticket_type_id, $seat_number, $status);
    
    if (!$stmt->execute()) {
        echo "Execute failed for row $i: " . $stmt->error . "<br>";
    }
}

echo "200 records inserted successfully";

// Đóng câu lệnh và kết nối
$stmt->close();
$conn->close();
?>
