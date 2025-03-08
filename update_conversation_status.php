<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nhận dữ liệu JSON từ body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true); // Chuyển đổi JSON thành mảng PHP

    $conversationId = $data['conversationId'] ?? null; // Lấy conversationId
    $status = $data['status'] ?? null; // Lấy status

    // Kiểm tra các tham số có tồn tại không
    if ($conversationId === null || $status === null) {
        echo json_encode(["success" => false, "message" => "Missing required parameters."]);
        exit; // Dừng script nếu thiếu tham số
    }

    // Thực hiện truy vấn SQL để cập nhật trạng thái cuộc trò chuyện
    $sql = "UPDATE conversations SET statusConver = ? WHERE conversation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $conversationId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating conversation status."]);
    }

    $stmt->close();
    $conn->close();
}
?>
