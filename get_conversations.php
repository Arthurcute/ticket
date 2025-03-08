<?php
include 'config.php'; 

// Chuẩn bị truy vấn SQL
$sql = "SELECT conversation_id, event_id, user_id, admin_id, statusConver, username FROM conversations"; 
$stmt = $conn->prepare($sql); // Chuẩn bị câu lệnh SQL

// Kiểm tra xem câu lệnh có được chuẩn bị thành công hay không
if ($stmt) {
    // Thực thi câu lệnh
    $stmt->execute();
    
    // Lấy kết quả
    $result = $stmt->get_result();

    // Kiểm tra nếu có kết quả
    if ($result->num_rows > 0) {
        $conversations = array();
        while ($row = $result->fetch_assoc()) {
            $conversations[] = $row;
        }
        echo json_encode($conversations);
    } else {
        echo json_encode(array()); // Trả về mảng rỗng nếu không có cuộc trò chuyện nào
    }

    $stmt->close(); // Đóng câu lệnh chuẩn bị
} else {
    // Xử lý lỗi nếu câu lệnh không thể chuẩn bị
    echo json_encode(array("success" => false, "message" => "Database error: " . $conn->error));
}

$conn->close(); // Đóng kết nối cơ sở dữ liệu
?>
