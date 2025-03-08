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

    $sql = "SELECT status FROM blocks WHERE conversation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $conversationId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(["success" => true, "status" => $row['status']]);
    } else {
        echo json_encode(["success" => false, "message" => "Conversation not blocked."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
