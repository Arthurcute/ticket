<?php
require 'config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : null;

    if (empty($conversation_id)) {
        echo json_encode([
            "error" => true,
            "message" => "Missing conversation_id"
        ]);
        exit();
    }

    $sql = "SELECT public_rsa_user FROM conversations WHERE conversation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $conversation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "error" => false,
            "public_rsa_user" => $row['public_rsa_user']
        ]);
    } else {
        echo json_encode([
            "error" => true,
            "message" => "Conversation not found"
        ]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        "error" => true,
        "message" => "Server error: " . $e->getMessage()
    ]);
    exit();
}
?>
