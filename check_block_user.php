<?php
include 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    if (empty($input['userId'])) {
        echo json_encode(["success" => false, "message" => "userId is required."]);
        exit();
    }

    $userId = $input['userId'];

    // Truy vấn trạng thái của người dùng
    $sql = "SELECT status FROM blocks WHERE user_id = ? AND status = 'Chặn'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Nếu có kết quả, trả về trạng thái bị chặn
        echo json_encode(["success" => true, "message" => "bị chặn"]);
    } else {
        // Nếu không có kết quả, trả về trạng thái không bị chặn
        echo json_encode(["success" => true, "message" => "không"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
