<?php
include 'config.php';

header('Content-Type: application/json'); // Đặt kiểu phản hồi là JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    if (empty($input['conversationId']) || empty($input['adminId']) || empty($input['userId']) || empty($input['des'])) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    $conversationId = $input['conversationId'];
    $adminId = $input['adminId'];
    $userId = $input['userId'];
    $des = $input['des'];

    $sql = "INSERT INTO blocks (user_id, admin_id, conversation_id, status, des, timeBlock) VALUES (?, ?, ?,'Chặn', ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $userId, $adminId, $conversationId, $des);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
$conn->close();
?>
