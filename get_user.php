<?php
// Kết nối cơ sở dữ liệu
include 'config.php';
// Kiểm tra nếu user_id được truyền vào
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Truy vấn thông tin người dùng từ bảng users
    $query = "SELECT user_id, name, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu tìm thấy người dùng
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(["message" => "User not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["message" => "User ID not provided"]);
}

// Đóng kết nối
$conn->close();
?>
