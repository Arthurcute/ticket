<?php
header('Content-Type: application/json');
include 'config.php'; 

// Lấy email và mật khẩu từ yêu cầu POST
$email = $_POST['email'];
$password = $_POST['password'];

// Tránh SQL injection bằng cách sử dụng prepared statements
$stmt = $conn->prepare("SELECT admin_id FROM admins WHERE email = ? AND password = ?");
$stmt->bind_param("ss", $email, $password);

$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra xem có admin nào với email và mật khẩu đã cho hay không
if ($result->num_rows > 0) {
    // Lấy admin_id từ kết quả
    $row = $result->fetch_assoc();
    echo json_encode(array("status" => "success", "admin_id" => $row['admin_id'])); // Đăng nhập thành công, trả về admin_id
} else {
    echo json_encode(array("status" => "failure")); // Đăng nhập không thành công
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>

