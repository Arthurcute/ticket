<?php
include 'config.php'; // Kết nối cơ sở dữ liệu

// Lấy nội dung JSON từ yêu cầu POST
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); // Chuyển đổi JSON thành mảng PHP

// Kiểm tra xem dữ liệu có được gửi đầy đủ hay không
if (empty($input['eventId']) || empty($input['userId']) || empty($input['userName']) || empty($input['publicKey'])) {
    echo json_encode([
        "success" => false, 
        "message" => "eventId, userId, userName, and publicKey cannot be empty."
    ]);
    exit(); // Dừng script nếu thiếu dữ liệu
}

// Lấy giá trị từ JSON
$eventId = $input['eventId'];
$userId = $input['userId'];
$userName = $input['userName'];
$publicKey = $input['publicKey']; // Dữ liệu publicKey là Base64 string

// Chuẩn bị câu lệnh SQL để chèn vào bảng conversations
$sql = "INSERT INTO conversations (event_id, user_id, username, statusConver, public_rsa_user) VALUES (?, ?, ?, 'pending', ?)";
$stmt = $conn->prepare($sql); // Chuẩn bị truy vấn SQL

if ($stmt) {
    // Gán giá trị cho các tham số (eventId là INT, userId và userName là STRING, publicKey là STRING)
    $stmt->bind_param("isss", $eventId, $userId, $userName, $publicKey);

    // Thực hiện câu lệnh SQL
    if ($stmt->execute()) {
        // Lấy ID của cuộc trò chuyện mới
        $conversationId = $stmt->insert_id;
        // Phản hồi thành công với conversation_id
        echo json_encode([
            "success" => true, 
            "conversation_id" => $conversationId
        ]);
    } else {
        // Phản hồi nếu có lỗi trong quá trình chèn dữ liệu
        echo json_encode([
            "success" => false, 
            "message" => "Error creating conversation: " . $stmt->error
        ]);
    }
    $stmt->close(); // Đóng câu lệnh chuẩn bị
} else {
    // Phản hồi nếu có lỗi trong quá trình chuẩn bị câu lệnh SQL
    echo json_encode([
        "success" => false, 
        "message" => "Database error: " . $conn->error
    ]);
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>
