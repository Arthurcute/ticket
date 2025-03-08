<?php
// Kết nối cơ sở dữ liệu
include 'config.php';

// Kiểm tra nếu admin_id được truyền vào
if (isset($_GET['admin_id'])) {
    $admin_id = $_GET['admin_id'];

    // Truy vấn thông tin người dùng từ bảng admins
    $query = "SELECT * FROM admins WHERE admin_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu tìm thấy người dùng
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo json_encode($admin);
    } else {
        echo json_encode(["message" => "Admin not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["message" => "Admin ID not provided"]);
}

// Đóng kết nối
$conn->close();
?>
