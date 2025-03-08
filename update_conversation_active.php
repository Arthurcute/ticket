<?php
// Kết nối tới cơ sở dữ liệu
include 'config.php'; 

// Kiểm tra xem các tham số cần thiết có được gửi hay không
if (isset($_POST['conversation_id']) && isset($_POST['admin_id']) && isset($_POST['status'])) {
    $conversationId = $_POST['conversation_id'];
    $adminId = $_POST['admin_id'];
    $status = $_POST['status'];
    $publicKeyAdmin = $_POST['publicKeyAdmin'];

    // Ghi lại để kiểm tra
    error_log("Received conversation_id: $conversationId, admin_id: $adminId, status: $status, publicKeyAdmin:$publicKeyAdmin" );

    // Kiểm tra tính hợp lệ của conversation_id và admin_id (phải là số dương)
    if (!is_numeric($conversationId) || $conversationId <= 0) {
        echo json_encode(array("error" => "ID cuộc trò chuyện không hợp lệ."));
        exit();
    }

    if (!is_numeric($adminId) || $adminId <= 0) {
        echo json_encode(array("error" => "ID quản trị viên không hợp lệ."));
        exit();
    }

    // Danh sách các trạng thái hợp lệ
    $validStatuses = array("open", "closed", "pending", "active"); // Thêm "active" vào danh sách

    // Kiểm tra tính hợp lệ của status
    if (!in_array($status, $validStatuses)) {
        echo json_encode(array("error" => "Trạng thái không hợp lệ."));
        exit();
    }

    // Chuẩn bị câu lệnh SQL để cập nhật admin_id và status của conversation
    $sql = "UPDATE conversations SET admin_id = ?, statusConver = ?,public_rsa_admin = ? WHERE conversation_id = ?";

    // Sử dụng Prepared Statement để tránh SQL Injection
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issi", $adminId, $status,$publicKeyAdmin, $conversationId);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            // Nếu cập nhật thành công, trả về thông báo thành công
            echo json_encode(array("message" => "Trạng thái và admin ID cập nhật thành công."));
        } else {
            // Nếu có lỗi xảy ra khi cập nhật
            echo json_encode(array("error" => "Lỗi khi cập nhật trạng thái."));
        }

        // Đóng Prepared Statement
        $stmt->close();
    } else {
        echo json_encode(array("error" => "Không thể chuẩn bị câu lệnh SQL."));
    }
} else {
    // Nếu thiếu tham số
    echo json_encode(array("error" => "Thiếu tham số conversation_id, admin_id hoặc status."));
}

// Đóng kết nối
$conn->close();
?>
