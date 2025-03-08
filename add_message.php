<?php
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy nội dung JSON từ yêu cầu POST
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // Chuyển đổi JSON thành mảng PHP

    // Kiểm tra xem dữ liệu có được gửi đầy đủ không
    if (empty($input['conversationId']) || empty($input['userId']) || empty($input['messageContent']) || empty($input['encrypted_aes_key']) || empty($input['encrypted_aes_key_admin'])) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Lấy dữ liệu từ JSON
    $conversationId = $input['conversationId'];
    $userId = $input['userId'];
    $messageContent = $input['messageContent'];
    $encrypted_aes_key=$input['encrypted_aes_key'];
    $encrypted_aes_key_admin = $input['encrypted_aes_key_admin'];


    // Thực hiện truy vấn SQL để thêm tin nhắn mới
    $sql = "INSERT INTO messages (conversation_id, sender_type, message_content,encrypted_aes_key,encrypted_aes_key_admin, timestamp) VALUES (?, 'user', ?, ?,?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $conversationId, $messageContent, $encrypted_aes_key,$encrypted_aes_key_admin);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding message."]);
    }

    $stmt->close();
}
$conn->close();
?>
