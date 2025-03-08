<?php
require 'config.php';
mysqli_set_charset($conn, "utf8");

// Đọc dữ liệu JSON từ body của yêu cầu
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra xem các khóa 'email' và 'code' có tồn tại trong mảng không
$email = isset($data['email']) ? htmlspecialchars($data['email']) : '';
$code = isset($data['code']) ? htmlspecialchars($data['code']) : '';

if (empty($email) || empty($code)) {
    echo json_encode(array("status" => "error", "message" => "Email và mã xác nhận là bắt buộc."));
    exit;
}

// Xác thực mã xác nhận và kiểm tra thời gian hết hạn
$stmt = $conn->prepare("SELECT * FROM pending_users WHERE email = ? AND verification_code = ? AND expiration_time > NOW()");
$stmt->bind_param("ss", $email, $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
    $hashed_password = password_hash($user['password'], PASSWORD_BCRYPT);

    // Thêm người dùng vào bảng users
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, birthday, gender) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $user['name'], $user['email'], $hashed_password, $user['phone'], $user['birthday'], $user['gender']);

    if ($stmt->execute()) {
        // Xóa khỏi bảng pending_users
        $stmt = $conn->prepare("DELETE FROM pending_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        echo json_encode(array("status" => "success", "message" => "Xác nhận thành công, tài khoản đã được đăng ký."));
    } else {
        echo json_encode(array("status" => "error", "message" => "Đã xảy ra lỗi trong khi hoàn thành đăng ký."));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Mã xác nhận không hợp lệ hoặc đã hết hạn."));
}

$stmt->close();
$conn->close();
?>
