<?php
header('Content-Type: application/json');
include 'config.php'; // Tệp config.php cần chứa mã kết nối MySQLi

// Lấy conversation_id từ yêu cầu
if (isset($_GET['conversation_id'])) {
    $conversation_id = $_GET['conversation_id'];

    // Truy vấn tin nhắn dựa trên conversation_id
    $query = "SELECT `message_id`, `conversation_id`, `sender_type`, `message_content`,`encrypted_aes_key`,`encrypted_aes_key_admin`, `timestamp` 
              FROM `messages` 
              WHERE `conversation_id` = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $conversation_id); // "i" cho integer
    mysqli_stmt_execute($stmt);

    // Lấy kết quả
    $result = mysqli_stmt_get_result($stmt);
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Kiểm tra nếu không có tin nhắn nào
    if ($messages) {
        echo json_encode($messages);
    } else {
        echo json_encode(["message" => "No messages found for this conversation."]);
    }

    // Đóng statement
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["error" => "conversation_id is required."]);
}

// Đóng kết nối
mysqli_close($conn);
?>
