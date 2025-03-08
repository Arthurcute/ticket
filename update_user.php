<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include 'config.php'; // Đảm bảo file config.php chứa các thông tin kết nối cơ sở dữ liệu
include_once 'aes_util.php'; // Giả sử aes_util.php có hàm mã hóa

// Nhận dữ liệu JSON
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra nếu dữ liệu hợp lệ
if (isset($data['userProfile'])) {
    $userProfile = $data['userProfile'];

    $userId = isset($userProfile['user_id']) ? intval($userProfile['user_id']) : null;
    $name = $userProfile['name'] ?? null;
    $email = isset($userProfile['email']) ? encrypt_aes($userProfile['email']) : null; // Mã hóa email nếu có
    $phone = isset($userProfile['phone']) ? encrypt_aes($userProfile['phone']) : null; // Mã hóa phone nếu có
    $birthdate = $userProfile['birth'] ?? null; // Đổi tên biến thành $birthdate
    $gender = $userProfile['gender'] ?? null;

    // Kiểm tra nếu các trường bắt buộc đều hợp lệ
    if ($userId && $name && $email) {
        // Cập nhật thông tin người dùng trong cơ sở dữ liệu
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, birthdate = ?, gender = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $birthdate, $gender, $userId); // Sử dụng $birthdate ở đây

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Cập nhật thông tin thành công"]);
        } else {
            // Thêm thông báo lỗi nếu câu lệnh SQL không thành công
            echo json_encode(["success" => false, "message" => "Cập nhật không thành công", "error" => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Thiếu thông tin bắt buộc (user_id, name, email)"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
}

$conn->close();
?>
