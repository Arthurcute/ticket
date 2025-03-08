
<?php
// Định nghĩa khóa bảo mật 32 byte cho AES-256
define('SECRET_KEY', 'abcdefghijklmnopqrstuvwxyzabcdef'); // Khóa bảo mật AES-256 (32 byte)
define('IV', '1234567890123456'); // IV cố định 16 byte

// Hàm mã hóa AES-256
function encrypt_aes($data) {
    // Mã hóa dữ liệu với AES-256-CBC và IV cố định
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', SECRET_KEY, 0, IV);
    
    // Kiểm tra nếu có lỗi trong quá trình mã hóa
    if ($encrypted === false) {
        return null; // Trả về null nếu có lỗi
    }
    
    // Trả về mã hóa base64
    return base64_encode($encrypted);
}

// Hàm giải mã AES-256
function decrypt_aes($data) {
    // Giải mã base64
    $data = base64_decode($data);
    
    // Giải mã dữ liệu với AES-256-CBC và IV cố định
    $decrypted = openssl_decrypt($data, 'aes-256-cbc', SECRET_KEY, 0, IV);
    
    // Kiểm tra nếu có lỗi trong quá trình giải mã
    if ($decrypted === false) {
        return null; // Trả về null nếu có lỗi
    }
    
    // Trả về dữ liệu giải mã
    return $decrypted;
}
?>
