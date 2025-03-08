<?php
header('Content-Type: application/json');
include 'config.php'; // Kết nối cơ sở dữ liệu
include_once 'aes_util.php'; // Chứa hàm mã hóa AES

// Lấy dữ liệu JSON từ yêu cầu
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    // Lặp qua từng người dùng trong mảng
    foreach ($data as $user) {
        $name = $user['name'];
        $email = encrypt_aes($user['email']); // Mã hóa email
        $password = password_hash($user['password'], PASSWORD_BCRYPT); // Mã hóa mật khẩu
        $phone = encrypt_aes($user['phone']); // Mã hóa số điện thoại
        $birthDate = $user['birthDate'];
        $gender = $user['gender'];

        // Kiểm tra xem email đã tồn tại chưa
        $checkEmailQuery = "SELECT * FROM Users WHERE email = ?";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode("Email đã được đăng ký");
            exit();
        }

        // Thêm người dùng vào cơ sở dữ liệu
        $insertQuery = "INSERT INTO Users (name, email, password, phone, birthDate, gender) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssss", $name, $email, $password, $phone, $birthDate, $gender);
        $stmt->execute();
    }

    echo json_encode("Đăng ký thành công");
} else {
    echo json_encode("Không có dữ liệu");
}
?>

