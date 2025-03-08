<?php
require 'config.php';

// Lấy dữ liệu từ yêu cầu POST
$user_id = $_POST['user_id'];
$event_id = $_POST['event_id'];

// Kiểm tra tính hợp lệ của dữ liệu
if (empty($user_id) || empty($event_id)) {
    echo json_encode([
        "error" => true,
        "message" => "Missing user_id or event_id"
    ]);
    exit();
}

// Truy vấn kiểm tra cuộc trò chuyện và lấy thông tin chi tiết
$sql = "SELECT conversation_id, statusConver, public_rsa_admin 
        FROM conversations 
        WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

$response = array();

if ($result->num_rows > 0) {
    // Cuộc trò chuyện đã tồn tại, trả về thông tin chi tiết
    $row = $result->fetch_assoc();
    $response = [
        "error" => false,
        "conversation_id" => $row['conversation_id'],
        "statusConver" => $row['statusConver'],
        "public_rsa_admin" => $row['public_rsa_admin'],
        "message" => "Conversation exists"
    ];
} else {
    // Cuộc trò chuyện chưa tồn tại
    $response = [
        "error" => false,
        "conversation_id" => null,
        "statusConver" => null,
        "public_rsa_admin" => null,
        "message" => "Conversation does not exist"
    ];
}

// Đóng kết nối và gửi phản hồi
$stmt->close();
$conn->close();

echo json_encode($response);
?>
