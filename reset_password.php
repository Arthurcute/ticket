<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'config.php'; // Kết nối cơ sở dữ liệu
include_once 'aes_util.php'; // Hàm mã hóa AES

// Lấy dữ liệu JSON từ body
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu có được gửi lên hay không
$email = isset($data['email']) ? $data['email'] : null;
$new_password = isset($data['new_password']) ? $data['new_password'] : null;

// Kiểm tra dữ liệu hợp lệ
if (!$email) {
    echo json_encode(["success" => false, "message" => "Email is missing."]);
    exit;
}

if (!$new_password) {
    echo json_encode(["success" => false, "message" => "New password is missing."]);
    exit;
}

// Mã hóa email để so sánh với cơ sở dữ liệu
$email_encrypted = encrypt_aes($email);

// Kiểm tra email có khớp trong cơ sở dữ liệu
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email_encrypted);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu người dùng tồn tại
if ($result->num_rows > 0) {
    // Lấy thông tin người dùng từ cơ sở dữ liệu
    $user = $result->fetch_assoc();

    // Mã hóa mật khẩu mới
    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

    // Cập nhật mật khẩu mới vào cột password
    $sql_update = "UPDATE users SET password = ? WHERE email = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ss", $new_password_hashed, $email_encrypted);

    if ($stmt_update->execute()) {
        echo json_encode(["success" => true, "message" => "Password updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update password."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid email."]);
}

$conn->close();
?>
