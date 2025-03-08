<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'config.php';
include_once 'aes_util.php'; // Hàm mã hóa AES (có thể thay đổi tùy theo phương thức mã hóa bạn sử dụng)

// Kiểm tra nếu có dữ liệu POST hợp lệ
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra xem email và OTP có hợp lệ không
if (empty($data['email']) || empty($data['otp'])) {
    echo json_encode(["status" => "error", "message" => "Email và OTP không hợp lệ."]);
    exit();
}

// Lấy email và OTP từ request
$email = $data['email'];
$otp = $data['otp'];

// Mã hóa email từ Postman (email người dùng nhập vào) để so sánh với email mã hóa trong cơ sở dữ liệu
$email_encrypted = encrypt_aes($email);

// Log giá trị email mã hóa và OTP nhận được
error_log("Encrypted email: " . $email_encrypted);
error_log("OTP from request: " . $otp);

// Truy vấn cơ sở dữ liệu để kiểm tra email và OTP
$query = "SELECT * FROM users WHERE email = ? AND otp_code = ? AND otp_expiry > NOW()";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $email_encrypted, $otp);  // Sử dụng email mã hóa khi truy vấn
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu có bản ghi
if ($result->num_rows === 0) {
    error_log("No records found or OTP expired for email: " . $email_encrypted);
    
    // Kiểm tra lại OTP và email trong cơ sở dữ liệu
    $debug_query = "SELECT email, otp_code, otp_expiry FROM users WHERE email = ?";
    $debug_stmt = $conn->prepare($debug_query);
    $debug_stmt->bind_param("s", $email_encrypted);
    $debug_stmt->execute();
    $debug_result = $debug_stmt->get_result();
    
    // Log thông tin debug
    while ($debug_row = $debug_result->fetch_assoc()) {
        error_log("Debug Info - Email: " . $debug_row['email'] . " OTP: " . $debug_row['otp_code'] . " Expiry: " . $debug_row['otp_expiry']);
    }
    
    echo json_encode(["status" => "error", "message" => "OTP không hợp lệ hoặc đã hết hạn."]);
    exit();
}

// Giải mã email từ cơ sở dữ liệu
$user = $result->fetch_assoc();
$email_decrypted = decrypt_aes($user['email']);  // Giải mã email từ cơ sở dữ liệu

// Log email đã giải mã
error_log("Decrypted email: " . $email_decrypted);

// Kiểm tra xem email giải mã có khớp với email nhập từ người dùng không
if ($email_decrypted !== $email) {
    echo json_encode(["status" => "error", "message" => "Email không khớp."]);
    exit();
}

// Xóa OTP đã xác minh khỏi cơ sở dữ liệu
$query = "UPDATE users SET otp_code = NULL, otp_expiry = NULL WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email_encrypted);
$stmt->execute();

// Trả về kết quả thành công
echo json_encode(["status" => "success", "message" => "Xác minh OTP thành công."]);

// ** Gửi OTP mới (Lấy thời gian hiện tại và thêm 10 phút vào thời gian hết hạn) **

// Lấy thời gian hiện tại để gửi OTP
$current_time = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));

// Cộng thêm 10 phút vào thời gian gửi OTP để xác định thời gian hết hạn
$expiry_time = $current_time->add(new DateInterval('PT10M'))->format('Y-m-d H:i:s');

// Lưu OTP và thời gian hết hạn vào cơ sở dữ liệu
$query = "UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $otp, $expiry_time, $email_encrypted);
$stmt->execute();

echo json_encode(["status" => "success", "message" => "OTP đã được gửi và thời gian hết hạn là $expiry_time"]);
?>
