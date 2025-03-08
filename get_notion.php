<?php
include 'config.php';
// Thiết lập kiểu dữ liệu trả về là JSON
header('Content-Type: application/json');

// Lấy ngày và thời gian hiện tại
$currentDate = date('Y-m-d');
$currentTime = date('Y-m-d H:i:s'); // Thời gian hiện tại với định dạng Y-m-d H:i:s

// Câu truy vấn để lấy tất cả thông tin của sự kiện có liên quan đến thời gian bán vé
$sql = "SELECT event_id, name, artist_id, start_time, end_time, location_id, event_type_id, 
               description, image_url, status, ticket_sale_start, ticket_sale_end
        FROM events
        WHERE (ticket_sale_start = '$currentDate' 
        OR DATE_SUB(ticket_sale_end, INTERVAL 1 DAY) = '$currentDate')";

$result = $conn->query($sql);

// Mảng để chứa dữ liệu thông báo
$eventNotions = array();

if ($result->num_rows > 0) {
    // Lặp qua kết quả truy vấn
    while($row = $result->fetch_assoc()) {
        $eventId = $row["event_id"];
        $eventName = $row["name"];
        $artistId = $row["artist_id"];
        $startTime = $row["start_time"];
        $endTime = $row["end_time"];
        $locationId = $row["location_id"];
        $eventTypeId = $row["event_type_id"];
        $description = $row["description"];
        $imageUrl = $row["image_url"];
        $status = $row["status"];
        $ticketSaleStart = $row["ticket_sale_start"];
        $ticketSaleEnd = $row["ticket_sale_end"];
        $content = "";

        // Xác định nội dung thông báo dựa trên thời gian
        if ($ticketSaleStart == $currentDate) {
            $content = "Đã mở bán vé sự kiện. Hãy mua vé tham gia ngay nào.";
        } else if (date('Y-m-d', strtotime($ticketSaleEnd . ' -1 day')) == $currentDate) {
            $content = "Sự kiện còn một ngày bán vé, hãy nhanh tay truy cập mua vé.";
        }

        // Thêm sự kiện vào mảng thông báo
        $eventNotions[] = array(
            "eventId" => $eventId,
            "eventName" => $eventName,
            "artistId" => $artistId,
            "startTime" => $startTime,
            "endTime" => $endTime,
            "locationId" => $locationId,
            "eventTypeId" => $eventTypeId,
            "description" => $description,
            "imageUrl" => $imageUrl,
            "status" => $status,
            "ticketSaleStart" => $ticketSaleStart,
            "ticketSaleEnd" => $ticketSaleEnd,
            "content" => $content
        );
    }
}

// Trả về dữ liệu JSON, bao gồm thời gian hiện tại
echo json_encode(array(
    "eventNotions" => $eventNotions,
    "currentTime" => $currentTime // Thêm thời gian hiện tại vào JSON
));

// Đóng kết nối
$conn->close();
?>
