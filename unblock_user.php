<?php
include 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    if (empty($input['conversationId'])) {
        echo json_encode(["success" => false, "message" => "conversationId is required."]);
        exit();
    }

    $conversationId = $input['conversationId'];

    $sql = "UPDATE blocks
            SET status = 'Bỏ chặn'
            WHERE conversation_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Failed to prepare statement."]);
        exit();
    }

    $stmt->bind_param("i", $conversationId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Thành công, có ít nhất một dòng được cập nhật
        echo json_encode(["success" => true, "message" => "Bỏ chặn thành công."]);
    } else {
        // Không có dòng nào được cập nhật
        echo json_encode(["success" => false, "message" => "Conversation not found or already unblocked."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
