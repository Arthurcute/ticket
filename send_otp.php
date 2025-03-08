

<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'config.php'; // Kết nối cơ sở dữ liệu
include_once 'aes_util.php'; // Chứa hàm mã hóa AES

require 'vendor/autoload.php'; // Bao gồm autoload của Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Lấy dữ liệu JSON từ frontend
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$email = mb_convert_encoding(trim($email), 'UTF-8', 'auto'); // Chuyển đổi sang UTF-8

// Kiểm tra email hợp lệ
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Định dạng email không hợp lệ."]);
    exit();
}

// Mã hóa email trước khi lưu vào cơ sở dữ liệu
$email_encrypted = encrypt_aes($email);
error_log("Email đã mã hóa: " . $email_encrypted);

// Kiểm tra email có tồn tại trong cơ sở dữ liệu không
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email_encrypted);
$stmt->execute();
$result = $stmt->get_result();

// Nếu email không tồn tại trong cơ sở dữ liệu
if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Email không tồn tại."]);
    error_log("Email không tồn tại trong cơ sở dữ liệu: " . $email_encrypted);
    exit();
}

// Lấy thông tin người dùng
$user = $result->fetch_assoc();
$email_decrypted = decrypt_aes($user['email']);
error_log("Email đã giải mã: " . $email_decrypted);

// Kiểm tra lại định dạng email sau khi giải mã
if (empty($email_decrypted) || !filter_var($email_decrypted, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Định dạng email không hợp lệ."]);
    exit();
}

// Tạo mã OTP ngẫu nhiên
$otp = bin2hex(random_bytes(3)); // Mã OTP 6 ký tự (3 byte)
error_log("Mã OTP được tạo: " . $otp);

// Lấy thời gian hiện tại và xác định thời gian hết hạn
$current_time = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
$expiry_time = $current_time->add(new DateInterval('PT10M'))->format('Y-m-d H:i:s');

// Lưu OTP và thời gian hết hạn vào cơ sở dữ liệu
$query = "UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $otp, $expiry_time, $email_encrypted);
$stmt->execute();

// Khởi tạo đối tượng PHPMailer
$mail = new PHPMailer(true);

try {
    // Cấu hình máy chủ SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Máy chủ SMTP của Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'ninhthuphuong9311@gmail.com'; // Thay đổi thành địa chỉ email của bạn
    $mail->Password = 'fror mdnh vdys kooq'; // Sử dụng app password nếu cần
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Cài đặt người gửi và người nhận
    $mail->setFrom('your_email@gmail.com', 'OTP Service'); // Địa chỉ gửi
    $mail->addAddress($email_decrypted); // Địa chỉ người nhận

    // Cấu hình nội dung email
    $mail->isHTML(true);
    $mail->Subject = 'Mã OTP xác nhận';
    $mail->Body = "
        <p>Xin chào,</p>
        <p>Mã OTP của bạn là: <b>$otp</b></p>
        <p>OTP sẽ hết hạn vào: $expiry_time</p>
        <p>Trân trọng,</p>
        <p>Đội ngũ hỗ trợ</p>
    ";

    // Gửi email
    $mail->send();
    echo json_encode(["status" => "success", "message" => "OTP đã được gửi đến email của bạn."]);
} catch (Exception $e) {
    error_log("Gửi email thất bại: " . $mail->ErrorInfo);
    echo json_encode(["status" => "error", "message" => "Gửi email thất bại. Vui lòng thử lại sau."]);
}

?>
