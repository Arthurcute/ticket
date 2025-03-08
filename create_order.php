<?php
header("Content-Type: application/json");

include 'config.php'; // Kết nối đến cơ sở dữ liệu

// Lấy dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents("php://input"));

// Kiểm tra dữ liệu nhận được
if ($data === null) {
    echo json_encode(["error" => "Invalid JSON received."]);
    exit;
}

// Kiểm tra các trường bắt buộc
$required_fields = ['user_id', 'payment_method_id', 'total_amount', 'order_date', 'event_id', 'ticket_type_id', 'quantity', 'price'];
foreach ($required_fields as $field) {
    if (!isset($data->$field)) {
        echo json_encode(["error" => "Missing field: $field"]);
        exit;
    }
}

// Lấy giá trị từ dữ liệu
$user_id = $data->user_id;
$payment_method_id = $data->payment_method_id;
$total_amount = $data->total_amount;
$event_id = $data->event_id;
$ticket_type_id = $data->ticket_type_id;
$quantity = $data->quantity;
$price = $data->price;

// Kiểm tra kiểu dữ liệu và các giá trị hợp lệ
if (!is_numeric($user_id) || !is_numeric($payment_method_id) || !is_numeric($event_id) || !is_numeric($ticket_type_id) || !is_numeric($quantity) || !is_numeric($price)) {
    echo json_encode(["error" => "Invalid input data. All IDs, quantity, and price must be numeric."]);
    exit;
}

// Kiểm tra số tiền, số lượng vé hợp lệ
if ($total_amount <= 0 || $quantity <= 0 || $price <= 0) {
    echo json_encode(["error" => "Invalid total amount, quantity, or price."]);
    exit;
}

// Lấy ngày hiện tại mà không có giờ
$order_date = date('Y-m-d');  // Chỉ lấy ngày, ví dụ: '2024-12-16'

// Tạo đơn hàng
$sql = "INSERT INTO orders (user_id, event_id, ticket_type_id, quantity, payment_method_id, order_date, total_amount, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
$stmt = $conn->prepare($sql);

// Bind các tham số (bao gồm giá trị cho order_date)
$stmt->bind_param("iiiiisd", $user_id, $event_id, $ticket_type_id, $quantity, $payment_method_id, $order_date, $total_amount);


if ($stmt->execute()) {
    // Lấy order_id vừa tạo
    $order_id = $stmt->insert_id;

    // Cập nhật số lượng vé có sẵn
    $sql_update = "UPDATE tickettypes SET available_quantity = available_quantity - ? WHERE ticket_type_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $quantity, $ticket_type_id);
    
    if ($stmt_update->execute()) {
        // Trả về order_id
        $response['order_id'] = $order_id;
    } else {
        $response['error'] = "Failed to update available quantity: " . $stmt_update->error;
    }

    $stmt_update->close();
} else {
    $response['error'] = "Failed to execute query: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Trả về phản hồi về client
echo json_encode($response);
?>
