<?php
require 'config.php';

// Lấy ticket_type_id từ GET request
$ticket_type_id = isset($_GET['ticket_type_id']) ? intval($_GET['ticket_type_id']) : 0;

// Truy vấn để lấy thông tin loại vé
$sql = "SELECT type_name FROM tickettypes WHERE ticket_type_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_type_id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra kết quả và trả về dữ liệu dưới dạng JSON
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response = array('type_name' => $row['type_name']);
    echo json_encode($response);
} else {
    echo json_encode(array('error' => 'Ticket type not found.'));
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>
