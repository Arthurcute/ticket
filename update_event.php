<?php
// Kết nối với cơ sở dữ liệu
require 'config.php'; // Chứa thông tin kết nối CSDL
$conn->set_charset("utf8");

// Lấy ngày hiện tại
$currentDate = date('Y-m-d H:i:s');
$currentDay = date('Y-m-d'); // Lấy chỉ phần ngày

// Cập nhật sự kiện đã kết thúc (end_time < ngày hiện tại)
$sqlFinished = "UPDATE events 
                SET status = 'đã diễn ra' 
                WHERE end_time < ? AND status != 'đã diễn ra'";

// Cập nhật sự kiện đang diễn ra (end_time = ngày hôm nay)
$sqlOngoing = "UPDATE events 
               SET status = 'đang diễn ra' 
               WHERE DATE(end_time) = ? AND status NOT IN ('đã diễn ra', 'đang diễn ra')";

// Chuẩn bị và thực thi truy vấn cập nhật sự kiện đã kết thúc
$stmtFinished = $conn->prepare($sqlFinished);
$stmtOngoing = $conn->prepare($sqlOngoing);

if ($stmtFinished && $stmtOngoing) {
    // Gán tham số và thực thi truy vấn
    $stmtFinished->bind_param("s", $currentDate);
    $stmtOngoing->bind_param("s", $currentDay);

    $stmtFinished->execute();
    $stmtOngoing->execute();

    // Kiểm tra số hàng bị ảnh hưởng
    $finishedCount = $stmtFinished->affected_rows;
    $ongoingCount = $stmtOngoing->affected_rows;

    // Đóng statement
    $stmtFinished->close();
    $stmtOngoing->close();

    // Chuẩn bị phản hồi
    $response = array(
        "success" => true,
        "message" => "Updated $finishedCount finished events, $ongoingCount ongoing events."
    );
} else {
    // Xử lý lỗi nếu không chuẩn bị được truy vấn
    $response = array(
        "success" => false,
        "message" => "Failed to prepare queries: " . $conn->error
    );
}

// Đóng kết nối
$conn->close();

// Trả về kết quả dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
