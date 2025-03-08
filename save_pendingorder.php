<?php
require 'config.php';
$conn->set_charset("utf8");

// Lấy dữ liệu từ yêu cầu POST
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Kiểm tra xem dữ liệu đã được phân tích thành công không
if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON data"]);
    exit;
}

// Lấy user_id và event_id từ dữ liệu
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$event_id = isset($data['event_id']) ? intval($data['event_id']) : 0;

// Kiểm tra dữ liệu user_id và event_id có hợp lệ không
if ($user_id <= 0 || $event_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user_id or event_id"]);
    exit;
}

// Lấy danh sách vé từ dữ liệu
$tickets = isset($data['tickets']) ? $data['tickets'] : [];

if (empty($tickets)) {
    echo json_encode(["success" => false, "message" => "No ticket data provided"]);
    exit;
}

// Prepare statement để chèn đơn hàng vào cơ sở dữ liệu
$stmt = $conn->prepare("INSERT INTO pending_order (user_id, event_id, number, ticket_type_id, price, total) VALUES (?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit;
}

// Duyệt qua danh sách vé và chèn vào cơ sở dữ liệu
foreach ($tickets as $ticket) {
    $ticket_type_id = isset($ticket['ticket_type_id']) ? intval($ticket['ticket_type_id']) : 0;
    $quantity = isset($ticket['quantity']) ? intval($ticket['quantity']) : 0;

    // Kiểm tra dữ liệu vé có hợp lệ không
    if ($ticket_type_id <= 0 || $quantity <= 0) {
        continue; 
    }

    // Lấy giá vé từ cơ sở dữ liệu
    $priceQuery = $conn->prepare("SELECT price FROM ticket_types WHERE ticket_type_id = ?");
    $priceQuery->bind_param("i", $ticket_type_id);
    $priceQuery->execute();
    $priceResult = $priceQuery->get_result();
    $price = 0;

    if ($priceResult->num_rows > 0) {
        $priceRow = $priceResult->fetch_assoc();
        $price = floatval($priceRow['price']);
    }

    // Tính toán tổng số tiền
    $total = $price * $quantity;

    // Thực hiện câu lệnh INSERT
    $stmt->bind_param("iiidd", $user_id, $event_id, $quantity, $ticket_type_id, $price, $total);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Execute failed: " . $stmt->error]);
        exit;
    }
}

$stmt->close();
$conn->close();

// Trả về phản hồi thành công
echo json_encode(["success" => true, "message" => "Order saved successfully"]);
?>
