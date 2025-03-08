<?php
// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';

$conn->set_charset("utf8");

// Lấy user_id từ tham số GET
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Kiểm tra xem user_id hợp lệ
if ($user_id <= 0) {
    echo json_encode(array("message" => "ID người dùng không hợp lệ."));
    exit();
}

// Chuẩn bị truy vấn để lấy thông tin vé cho user_id cụ thể
$sql = "SELECT * FROM orders WHERE user_id = $user_id"; // Thay đổi nếu cần
$result = $conn->query($sql);

$orders = array(); // Mảng để lưu thông tin vé

if ($result->num_rows > 0) {
    // Lặp qua các hàng dữ liệu và thêm vào mảng
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} else {
    echo json_encode(array("message" => "Không có vé nào được tìm thấy."));
    exit();
}

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($orders);

// Đóng kết nối
$conn->close();
?>
