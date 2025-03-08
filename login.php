<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'config.php';
include_once 'aes_util.php'; // Chứa hàm mã hóa AES

// Kiểm tra kết nối cơ sở dữ liệu
if ($conn->connect_error) {
    echo json_encode(array("success" => false, "message" => "Lỗi kết nối cơ sở dữ liệu"));
    exit();
}

// Lấy dữ liệu từ request
$data = json_decode(file_get_contents("php://input"));

if (isset($data->email) && isset($data->password)) {
    // Mã hóa email người dùng nhập vào
    $email_encrypted = encrypt_aes($data->email);  // Mã hóa email người dùng nhập vào

    // Kiểm tra mật khẩu người dùng nhập vào
    $password = $conn->real_escape_string($data->password); 

    // Sử dụng Prepared Statement để tránh SQL Injection
    $sql = "SELECT user_id, email, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email_encrypted); // So sánh với email đã mã hóa trong CSDL
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($password, $row['password'])) {
                echo json_encode(array("success" => true, "user_id" => $row['user_id'], "message" => "Đăng nhập thành công"));
            } else {
                echo json_encode(array("success" => false, "message" => "Mật khẩu không chính xác"));
            }
        } else {
            echo json_encode(array("success" => false, "message" => "Email không tồn tại"));
        }

        $stmt->close();
    } else {
        echo json_encode(array("success" => false, "message" => "Lỗi truy vấn cơ sở dữ liệu"));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Thiếu thông tin"));
}

$conn->close();
?>
