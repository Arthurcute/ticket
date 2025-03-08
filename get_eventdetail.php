<?php
// Kết nối với cơ sở dữ liệu
include 'config.php'; // Tập tin này chứa kết nối cơ sở dữ liệu

header('Content-Type: application/json');

// Kiểm tra nếu có tham số event_id được truyền vào
if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']); // Chuyển đổi event_id thành kiểu số nguyên

    // Chuẩn bị câu truy vấn SQL để lấy thông tin sự kiện
    $sql = "SELECT event_id, name, artist_id, start_time, end_time, 
                   location_id, event_type_id, description, image_url, 
                   status, ticket_sale_start, ticket_sale_end 
            FROM events 
            WHERE event_id = ?";

    // Chuẩn bị truy vấn
    if ($stmt = $conn->prepare($sql)) {
        // Gán giá trị event_id vào truy vấn
        $stmt->bind_param("i", $eventId);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            // Lấy kết quả truy vấn
            $result = $stmt->get_result();

            // Kiểm tra xem có dòng kết quả nào không
            if ($result->num_rows > 0) {
                // Chuyển đổi kết quả thành dạng mảng kết hợp
                $event = $result->fetch_assoc();

                // Đảm bảo rằng các thời gian được chuyển đổi đúng định dạng
                $event['start_time'] = date('Y-m-d H:i:s', strtotime($event['start_time']));
                $event['end_time'] = date('Y-m-d H:i:s', strtotime($event['end_time']));
                $event['ticket_sale_start'] = date('Y-m-d H:i:s', strtotime($event['ticket_sale_start']));
                $event['ticket_sale_end'] = date('Y-m-d H:i:s', strtotime($event['ticket_sale_end']));

                // Trả về dữ liệu dạng JSON
                echo json_encode($event);
            } else {
                // Không tìm thấy sự kiện
                echo json_encode(["error" => "Event not found"]);
            }
        } else {
            // Xử lý lỗi khi thực thi truy vấn thất bại
            echo json_encode(["error" => "Failed to execute the SQL statement: " . $stmt->error]);
        }

        // Đóng câu truy vấn
        $stmt->close();
    } else {
        // Xử lý lỗi khi chuẩn bị truy vấn thất bại
        echo json_encode(["error" => "Failed to prepare the SQL statement: " . $conn->error]);
    }
} else {
    // Thông báo lỗi nếu không có tham số event_id hoặc nó rỗng
    echo json_encode(["error" => "Event ID is required"]);
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>
