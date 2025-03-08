<?php
require 'config.php';
$conn->set_charset("utf8");
header('Content-Type: application/json; charset=utf-8');

// Lấy event_id từ tham số GET
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id > 0) {
    // Câu lệnh SQL với điều kiện WHERE
    $sql = "SELECT `ticket_type_id`, `event_id`, `type_name`, `price`, `total_quantity`, `available_quantity` FROM `tickettypes`
            WHERE event_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Gán giá trị cho tham số
        $stmt->bind_param("i", $event_id);

        // Thực thi câu lệnh
        $stmt->execute();
        $result = $stmt->get_result();

        $ticket_types = array();

        while ($row = $result->fetch_assoc()) {
            $ticket_types[] = $row;
        }

        // Gửi dữ liệu dưới dạng JSON
        echo json_encode($ticket_types);

        $stmt->close();
    } else {
        echo json_encode(array("error" => "Không thể chuẩn bị câu lệnh SQL"));
    }
} else {
    // Kiểm tra giá trị event_id
    echo json_encode(array("error" => "event_id không hợp lệ", "event_id" => $event_id));
}

$conn->close();
?>
